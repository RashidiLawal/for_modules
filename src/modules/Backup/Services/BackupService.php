<?php

declare(strict_types=1);

namespace Modules\Backup\Services;

use Modules\Backup\Repositories\BackupRepositoryInterface;
use ZipArchive;
use Exception;

/**
 * Service for handling backup and restore operations (files and database).
 *
 * - createBackup: Creates a backup of files and/or database.
 * - restoreBackup: Restores files and/or database from a backup point.
 */
class BackupService
{
    /** @var BackupRepositoryInterface */
    protected $backupRepository;

    public function __construct(BackupRepositoryInterface $backupRepository)
    {
        $this->backupRepository = $backupRepository;
    }

    /**
     * Create a backup (files and/or database).
     *
     * @param array $data
     * @return array
     */
    public function createBackup(array $data): array
    {
        $type = $data['type'] ?? 'files';
        $disk = $data['disk'] ?? 'local';
        $paths = $data['paths'] ?? [];
        $dbConnection = $data['database_connection'] ?? null;
        $timestamp = date('Ymd_His');
        $status = 'pending';
        $backupFileName = "backup_{$timestamp}_{$type}.zip";
        $backupDir = 'backups';
        $backupPath = "$backupDir/$backupFileName";
        $meta = [];
        $zip = new ZipArchive();
        $tmpFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $backupFileName;
        $dbDumpFile = null;
        try {
            if ($zip->open($tmpFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new Exception('Could not create zip archive');
            }
            // Add files to archive
            if ($type === 'files' || $type === 'both') {
                foreach ($paths as $path) {
                    if (is_file($path)) {
                        $zip->addFile($path, basename($path));
                    } elseif (is_dir($path)) {
                        $this->addDirToZip($zip, $path, basename($path));
                    }
                }
            }
            // Add database dump
            if ($type === 'database' || $type === 'both') {
                $dbDumpFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . "db_{$timestamp}.sql";
                $this->dumpDatabase($dbDumpFile, $dbConnection);
                $zip->addFile($dbDumpFile, basename($dbDumpFile));
            }
            $zip->close();
            // Store zip to storage disk
            $fileContents = file_get_contents($tmpFile);
            if ($disk === 'gdrive') {
                if (!class_exists('Hypweb\\Flysystem\\GoogleDrive\\GoogleDriveAdapter')) {
                    throw new Exception('Google Drive adapter not installed. Run: composer require masbug/flysystem-google-drive-ext');
                }
                storage('gdrive')->put($backupPath, $fileContents);
            } else {
                storage($disk)->put($backupPath, $fileContents);
            }
            $status = 'completed';
            // Clean up temp files
            @unlink($tmpFile);
            if ($dbDumpFile) {
                @unlink($dbDumpFile);
            }
            // Save metadata
            $backup = $this->backupRepository->create([
                'type' => $type,
                'path' => $backupPath,
                'disk' => $disk,
                'status' => $status,
                'meta' => $meta,
            ]);
            return [
                'status' => true,
                'message' => 'Backup created successfully',
                'backup' => $backup,
            ];
        } catch (Exception $e) {
            // Clean up temp files on error
            @unlink($tmpFile);
            if ($dbDumpFile) {
                @unlink($dbDumpFile);
            }
            return [
                'status' => false,
                'message' => 'Backup failed: ' . $e->getMessage(),
                'backup' => null,
            ];
        }
    }

    /**
     * Recursively add a directory to a ZipArchive.
     *
     * @param ZipArchive $zip
     * @param string $dir
     * @param string $base
     * @return void
     */
    protected function addDirToZip(ZipArchive $zip, string $dir, string $base): void
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($files as $file) {
            $localName = $base . DIRECTORY_SEPARATOR . substr($file, strlen($dir) + 1);
            if ($file->isDir()) {
                $zip->addEmptyDir($localName);
            } else {
                $zip->addFile($file, $localName);
            }
        }
    }

    /**
     * Dump the database to a file using mysqldump (for MySQL).
     *
     * @param string $outputFile
     * @param string|null $connection
     * @return void
     * @throws Exception
     */
    protected function dumpDatabase(string $outputFile, ?string $connection = null): void
    {
        // Get DB config from env or config
        $db = db($connection);
        $config = $db->getConfig();
        $driver = $config['driver'] ?? 'mysql';
        if ($driver !== 'mysql') {
            throw new Exception('Only MySQL database dumps are supported in this version.');
        }
        $host = $config['host'] ?? 'localhost';
        $port = $config['port'] ?? 3306;
        $username = $config['username'] ?? '';
        $password = $config['password'] ?? '';
        $database = $config['database'] ?? '';
        $cmd = sprintf(
            'mysqldump --user=%s --password=%s --host=%s --port=%d %s > %s',
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($host),
            $port,
            escapeshellarg($database),
            escapeshellarg($outputFile)
        );
        $result = null;
        system($cmd, $result);
        if ($result !== 0 || !file_exists($outputFile)) {
            throw new Exception('Database dump failed.');
        }
    }

    /**
     * Restore from a backup point (files and/or database).
     *
     * @param array $data
     * @return array
     */
    public function restoreBackup(array $data): array
    {
        $backupId = $data['backup_id'] ?? null;
        $type = $data['type'] ?? 'files';
        if (!$backupId) {
            return [
                'status' => false,
                'message' => 'Missing backup_id',
            ];
        }
        $backup = $this->backupRepository->findById($backupId);
        if (!$backup) {
            return [
                'status' => false,
                'message' => 'Backup not found',
            ];
        }
        $disk = $backup->disk ?? 'local';
        $backupPath = $backup->path ?? $backup->file_path ?? null;
        if (!$backupPath) {
            return [
                'status' => false,
                'message' => 'Backup file path not found',
            ];
        }
        $tmpFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . basename($backupPath);
        try {
            // Download backup archive from storage
            $fileContents = storage($disk)->get($backupPath);
            file_put_contents($tmpFile, $fileContents);
            $zip = new \ZipArchive();
            if ($zip->open($tmpFile) !== true) {
                throw new \Exception('Could not open backup archive');
            }
            // Restore files
            if ($type === 'files' || $type === 'both') {
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $entry = $zip->getNameIndex($i);
                    if (substr($entry, -4) !== '.sql') {
                        // Extract file to original location (WARNING: overwrites!)
                        $this->extractZipEntry($zip, $entry, getcwd());
                    }
                }
            }
            // Restore database
            if ($type === 'database' || $type === 'both') {
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $entry = $zip->getNameIndex($i);
                    if (substr($entry, -4) === '.sql') {
                        $sqlTmp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $entry;
                        file_put_contents($sqlTmp, $zip->getFromName($entry));
                        $this->restoreDatabase($sqlTmp);
                        @unlink($sqlTmp);
                    }
                }
            }
            $zip->close();
            @unlink($tmpFile);
            return [
                'status' => true,
                'message' => 'Backup restored successfully',
            ];
        } catch (\Exception $e) {
            @unlink($tmpFile);
            return [
                'status' => false,
                'message' => 'Restore failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Extract a file or directory from a ZipArchive to a target directory.
     *
     * @param ZipArchive $zip
     * @param string $entry
     * @param string $targetDir
     * @return void
     */
    protected function extractZipEntry(\ZipArchive $zip, string $entry, string $targetDir): void
    {
        $fullPath = $targetDir . DIRECTORY_SEPARATOR . $entry;
        if (substr($entry, -1) === '/') {
            // Directory
            if (!is_dir($fullPath)) {
                mkdir($fullPath, 0777, true);
            }
        } else {
            // File
            $dir = dirname($fullPath);
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
            file_put_contents($fullPath, $zip->getFromName($entry));
        }
    }

    /**
     * Restore a MySQL database from a SQL dump file.
     *
     * @param string $sqlFile
     * @param string|null $connection
     * @return void
     * @throws Exception
     */
    protected function restoreDatabase(string $sqlFile, ?string $connection = null): void
    {
        $db = db($connection);
        $config = $db->getConfig();
        $driver = $config['driver'] ?? 'mysql';
        if ($driver !== 'mysql') {
            throw new Exception('Only MySQL database restore is supported in this version.');
        }
        $host = $config['host'] ?? 'localhost';
        $port = $config['port'] ?? 3306;
        $username = $config['username'] ?? '';
        $password = $config['password'] ?? '';
        $database = $config['database'] ?? '';
        $cmd = sprintf(
            'mysql --user=%s --password=%s --host=%s --port=%d %s < %s',
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($host),
            $port,
            escapeshellarg($database),
            escapeshellarg($sqlFile)
        );
        $result = null;
        system($cmd, $result);
        if ($result !== 0) {
            throw new Exception('Database restore failed.');
        }
    }
} 