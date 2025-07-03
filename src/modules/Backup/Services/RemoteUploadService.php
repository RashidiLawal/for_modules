<?php

declare(strict_types=1);

namespace Modules\Backup\Services;

use Modules\Backup\Repositories\BackupRepositoryInterface;
use Exception;

/**
 * Service for uploading backups to remote storage providers (S3, Google Drive, OneDrive, Wasabi, SSH, FTP).
 *
 * - upload: Uploads a backup to the specified remote provider.
 *
 * Note: Use Flysystem adapters or provider SDKs as needed.
 */
class RemoteUploadService
{
    /** @var BackupRepositoryInterface */
    protected $backupRepository;

    public function __construct(BackupRepositoryInterface $backupRepository)
    {
        $this->backupRepository = $backupRepository;
    }

    /**
     * Upload a backup to remote storage (S3, Google Drive, etc.).
     *
     * @param array $data
     * @return array
     */
    public function upload(array $data): array
    {
        $backupId = $data['backup_id'] ?? null;
        $provider = $data['provider'] ?? null;
        $credentials = $data['credentials'] ?? [];
        if (!$backupId || !$provider) {
            return [
                'status' => false,
                'message' => 'Missing backup_id or provider',
            ];
        }
        $backup = $this->backupRepository->findById($backupId);
        if (!$backup) {
            return [
                'status' => false,
                'message' => 'Backup not found',
            ];
        }
        $localDisk = $backup->disk ?? 'local';
        $backupPath = $backup->path ?? $backup->file_path ?? null;
        if (!$backupPath) {
            return [
                'status' => false,
                'message' => 'Backup file path not found',
            ];
        }
        try {
            // Read backup file from local disk
            $fileContents = storage($localDisk)->get($backupPath);
            // Upload to remote provider
            $remotePath = $backupPath; // Use same path on remote
            // Optionally: configure credentials for provider (not shown here)
            storage($provider)->put($remotePath, $fileContents);
            // Update backup record
            $this->backupRepository->update($backupId, [
                'uploaded_to' => $provider,
                'status' => 'uploaded',
            ]);
            return [
                'status' => true,
                'message' => 'Backup uploaded to remote storage successfully',
                'provider' => $provider,
                'remote_path' => $remotePath,
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'message' => 'Upload failed: ' . $e->getMessage(),
            ];
        }
    }
} 