<?php

namespace BitCore\Tests\Feature;

use BitCore\Foundation\Filesystem\Filesystem;
use BitCore\Tests\TestCase;
use BitCore\Foundation\Filesystem\FilesystemManager;
use Mockery;

class FilesystemS3Test extends TestCase
{
    /**
     * @var Mockery\MockInterface
     */
    protected $filesystemManager;

    /**
     * @var Mockery\MockInterface
     */
    protected $mockFilesystem;

    /**
     * @var array
     */
    protected $config;

    /**
     * Setup method to initialize the mock objects
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Make app instance
        $this->getAppInstance();

        // Mock the FilesystemManager
        $this->filesystemManager = Mockery::mock(FilesystemManager::class)->makePartial()
            ->shouldAllowMockingProtectedMethods();

        // Mock the Filesystem instance that will be returned by disk()
        $this->mockFilesystem = Mockery::mock(Filesystem::class);

        // Define the configuration for S3
        $this->config = config()->get('filesystems', []);

        // Mock `getConfig` to return the configuration for S3
        $this->filesystemManager->shouldReceive('getConfig')
            ->andReturn($this->config['disks']['s3'] ?? []);

        // Mock `disk()` to return the mocked Filesystem instance for 's3'
        $this->filesystemManager->shouldReceive('disk')
            ->with('s3')
            ->andReturn($this->mockFilesystem);

        // Mock the 'put' method on the Filesystem instance to simulate a successful upload
        $this->mockFilesystem->shouldReceive('put')
            ->once()
            ->with('test_s3.txt', 'This is a test file for the S3 disk.')
            ->andReturn(true);

        // Mock the 'get' method to simulate getting file content from the disk
        $this->mockFilesystem->shouldReceive('get')
            ->once()
            ->with('test_s3.txt')
            ->andReturn('This is a test file for the S3 disk.');

        // Mock the 'exists' method to simulate checking if a file exists on the S3 disk
        $this->mockFilesystem->shouldReceive('exists')
            ->once()
            ->with('test_s3.txt')
            ->andReturn(true);

        // Mock the 'delete' method to simulate deleting a file on the S3 disk
        $this->mockFilesystem->shouldReceive('delete')
            ->once()
            ->with('test_s3.txt')
            ->andReturn(true);
    }

    /**
     * Test file operations on the S3 disk.
     */
    public function testS3DiskOperations()
    {
        // Now we use the mocked disk
        $s3Disk = $this->filesystemManager->disk('s3');

        // Define test file and content
        $testFileName = 'test_s3.txt';
        $testFileContent = 'This is a test file for the S3 disk.';

        // Write a file to the S3 disk
        $s3Disk->put($testFileName, $testFileContent);

        // Assert that the file exists
        $this->assertTrue($s3Disk->exists($testFileName));

        // Assert that the file content is correct
        $this->assertEquals($testFileContent, $s3Disk->get($testFileName));

        // Delete the file
        $s3Disk->delete($testFileName);
    }

    /**
     * Cleanup method to close Mockery after each test
     */
    protected function tearDown(): void
    {
        // Close Mockery to clean up after each test
        Mockery::close();

        parent::tearDown();
    }
}
