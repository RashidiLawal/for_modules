<?php

namespace BitCore\Tests\Feature;

use BitCore\Tests\TestCase;

class FilesystemTest extends TestCase
{
    /**
     * Test file operations on the local disk.
     */
    public function testTocalDiskOperations()
    {
        $localDisk = storage();

        // Define test file and content
        $testFileName = 'test_local.txt';
        $testFileContent = 'This is a test file for the local disk.';

        // Write a file to the local disk
        $localDisk->put($testFileName, $testFileContent);

        // Assert file exists
        $this->assertTrue($localDisk->exists($testFileName));

        // Assert file content is correct
        $this->assertEquals($testFileContent, $localDisk->get($testFileName));

        // Delete the file
        $localDisk->delete($testFileName);

        // Assert file is deleted
        $this->assertFalse($localDisk->exists($testFileName));
    }
}
