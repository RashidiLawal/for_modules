<?php

declare(strict_types=1);

namespace BitCore\Application\Services\Requests;

use Psr\Http\Message\UploadedFileInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile as SymfonyUploadedFile;

/**
 * Class to represent file upload.
 * This inherit from Symfony UploadedFile to ensure compatibility with illuminate validator.
 */
class UploadedFile extends SymfonyUploadedFile
{
    /**
     * @var UploadedFileInterface The PSR-7 file instance.
     */
    protected UploadedFileInterface $psr7File;

    /**
     * Set the PSR-7 file instance.
     *
     * @param UploadedFileInterface $file The PSR-7 file instance to set.
     */
    public function setPsr7File(UploadedFileInterface $file): void
    {
        $this->psr7File = $file;
    }

    /**
     * Return the PSR-7 file instance.
     *
     * @return UploadedFileInterface $file The PSR-7 file instance to set.
     */
    public function getPsr7File(): UploadedFileInterface
    {
        return $this->psr7File;
    }

    /**
     * Validate if the uploaded file is valid.
     *
     * @return bool Always returns true in this implementation.
     */
    public function isValid(): bool
    {
        $isOk = \UPLOAD_ERR_OK === $this->getError();

        return $isOk && is_file($this->getPathname());
    }

    /**
     * Create a new instance of FileUpload from a PSR-7 file instance.
     *
     * This method transfers the uploaded file's contents to a temporary location and
     * creates a new instance of FileUpload.
     *
     * @param UploadedFileInterface $file The PSR-7 file instance to wrap.
     * @return UploadedFile The new FileUpload instance.
     */
    public static function fromPsr7File(UploadedFileInterface $file): UploadedFile
    {
        // Clone the PSR-7 file to preserve the original object for future uploads
        $_file = clone $file;

        // Create a temporary file for the upload.
        $tempFilePath = tempnam(sys_get_temp_dir(), 'upload');

        // Move the file to the temporary location.
        $_file->moveTo($tempFilePath);

        // Create a new instance of FileUpload.
        $instance = new self(
            $tempFilePath,             // The temporary file path.
            $_file->getClientFilename(), // The original filename.
            null,                       // MIME type is not set in this case.
            $_file->getError(),         // Error code.
        );

        // Set the PSR-7 file instance on the new object.
        $instance->setPsr7File($file);

        return $instance;
    }

    /**
     * Store the uploaded file in a specified directory.
     *
     * This method delegates the file upload to the appropriate file uploader,
     * passing the destination directory and disk type.
     *
     * @param string $directory The target directory where the file will be uploaded.
     * @param string|null $disk The storage disk to use for the upload (e.g., 's3', 'local', 'public').
     *                          If not specified, the default disk will be used.
     * @return string The file path of the uploaded file.
     */
    public function store(string $directory = '', ?string $disk = null): string
    {
        return upload($directory, $this->psr7File, $disk);
    }
}
