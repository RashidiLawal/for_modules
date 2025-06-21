<?php

declare(strict_types=1);

namespace BitCore\Application\Services;

use BitCore\Foundation\Filesystem\FilesystemInterface;
use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;

/**
 * Class FileUploader
 *
 * Handles file uploads with support for chunked uploads.
 */
class FileUploader
{
    /** @var FilesystemInterface $filesystem */
    protected $filesystem;

    /** @var int Default chunk size for uploads (1MB). */
    protected int $chunkSize = 1048576;

    /** @var int Maximum size for single-file uploads (10MB). */
    protected int $maxChunkSize = 10 * 1024 * 1024;

    /**
     * Constructor.
     *
     * @param FilesystemInterface $filesystem Filesystem instance for handling file operations.
     */
    public function __construct($filesystem)
    {
        $this->useFilesystem($filesystem);
    }

    /**
     * Sets the filesystem to be used for uploads.
     *
     * @param FilesystemInterface $filesystem
     * @return self
     */
    public function useFilesystem($filesystem): self
    {
        $this->filesystem = $filesystem;
        return $this;
    }

    /**
     * Uploads a file to the specified directory, automatically deciding
     * whether to use chunked or single upload based on file size.
     *
     * @param string $directory Target directory for the uploaded file.
     * @param UploadedFileInterface $uploadedFile File to be uploaded.
     * @return string Path to the uploaded file.
     * @throws RuntimeException If the upload process fails.
     */
    public function uploadFile(string $directory, UploadedFileInterface $uploadedFile): string
    {
        $fileSize = $uploadedFile->getSize();
        $filePath = rtrim($directory, '/') . '/' . $this->generateUniqueFileName($uploadedFile);
        $uploadMethod = $this->getUploadMethod($fileSize);

        if ($uploadMethod === 'chunk') {
            $this->performChunkedUpload($uploadedFile, $filePath);
        } else {
            $this->performSingleUpload($uploadedFile, $filePath);
        }

        return $filePath;
    }

    /**
     * Determines the appropriate upload method (chunked or single) based on file size.
     *
     * @param int $fileSize Size of the file in bytes.
     * @return string Either 'chunk' or 'single'.
     */
    protected function getUploadMethod(int $fileSize): string
    {
        return $fileSize > $this->maxChunkSize ? 'chunk' : 'single';
    }

    /**
     * Handles chunked file uploads.
     *
     * @param UploadedFileInterface $uploadedFile File to be uploaded.
     * @param string $filePath Destination path for the uploaded file.
     * @param ?string $progressKey Optional key to track upload progress.
     * @throws RuntimeException If an error occurs during the upload process.
     */
    public function performChunkedUpload(
        UploadedFileInterface $uploadedFile,
        string $filePath,
        ?string $progressKey = null
    ): void {

        try {
            $stream = $uploadedFile->getStream();
            $fileSize = $uploadedFile->getSize();
            $chunkIndex = 0;
            $chunkSize = $this->calculateChunkSize($fileSize);
            $totalChunks = (int)ceil($fileSize / $chunkSize);
            $tempFilePath = $filePath . '.part';

            while (!$stream->eof()) {
                $chunkData = $stream->read($chunkSize);
                $this->filesystem->append($tempFilePath, $chunkData);
                $chunkIndex++;

                if ($progressKey) {
                    if ($chunkIndex === 1) {
                        $this->clearUploadProgress($progressKey);
                    }
                    $progress = $this->calculateProgress($chunkIndex, $totalChunks);
                    $this->updateSession(
                        $progressKey,
                        ['progress' => $progress, 'chunks' => $chunkIndex]
                    );
                }
            }

            $this->filesystem->move($tempFilePath, $filePath);
        } catch (\Throwable $e) {
            throw new RuntimeException("Chunked upload failed: " . $e->getMessage());
        } finally {
            if (isset($stream)) {
                $stream->close();
            }
        }
    }

    /**
     * Handles single file uploads.
     *
     * @param UploadedFileInterface $uploadedFile File to be uploaded.
     * @param string $filePath Destination path for the uploaded file.
     * @throws RuntimeException If an error occurs during the upload process.
     */
    public function performSingleUpload(UploadedFileInterface $uploadedFile, string $filePath): void
    {
        try {
            $stream = $uploadedFile->getStream();
            $this->filesystem->put($filePath, $stream->getContents());
        } catch (\Throwable $e) {
            throw new RuntimeException("Single upload failed: " . $e->getMessage());
        } finally {
            if (isset($stream)) {
                $stream->close();
            }
        }
    }

    /**
     * Calculates the appropriate chunk size based on the file size.
     *
     * @param int $fileSize Size of the file in bytes.
     * @return int Chunk size in bytes.
     */
    protected function calculateChunkSize(int $fileSize): int
    {
        if ($fileSize <= 10 * 1024 * 1024) {
            return 1 * 1024 * 1024;
        } elseif ($fileSize <= 100 * 1024 * 1024) {
            return 5 * 1024 * 1024;
        } elseif ($fileSize <= 1 * 1024 * 1024 * 1024) {
            return 10 * 1024 * 1024;
        }
        return 50 * 1024 * 1024;
    }

    /**
     * Calculates upload progress as a percentage.
     *
     * @param int $uploadedChunks Number of chunks uploaded.
     * @param int $totalChunks Total number of chunks.
     * @return int Progress percentage.
     */
    protected function calculateProgress(int $uploadedChunks, int $totalChunks): int
    {
        return (int)round(($uploadedChunks / $totalChunks) * 100);
    }

    /**
     * Generates a unique file name for the uploaded file.
     *
     * @param UploadedFileInterface $uploadedFile Uploaded file.
     * @return string Unique file name.
     */
    protected function generateUniqueFileName(UploadedFileInterface $uploadedFile): string
    {
        $originalFileName = $uploadedFile->getClientFilename();
        $extension = pathinfo($originalFileName, PATHINFO_EXTENSION);
        $uniqueName = uniqid(pathinfo($originalFileName, PATHINFO_FILENAME) . '_', true);

        return $uniqueName . ($extension ? '.' . $extension : '');
    }

    /**
     * Clears upload progress for a session key.
     *
     * @param string $progressKey Session key to clear.
     */
    public function clearUploadProgress(string $progressKey): void
    {
        $this->updateSession($progressKey, null);
    }

    /**
     * Manages session data for progress tracking.
     *
     * @param string|null $key Session key.
     * @param mixed|null $value Value to store in session.
     * @return mixed
     */
    protected function updateSession(?string $key = null, $value = null)
    {
        if ($value !== null) {
            $_SESSION[$key] = $value;
            return;
        }

        return $_SESSION[$key] ?? null;
    }
}
