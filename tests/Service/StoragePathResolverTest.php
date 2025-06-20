<?php

namespace App\Tests\Service;

use App\Service\StoragePathResolver;
use PHPUnit\Framework\TestCase;

class StoragePathResolverTest extends TestCase
{
    private string $tempProjectDir;

    protected function setUp(): void
    {
        $this->tempProjectDir = sys_get_temp_dir() . '/storage_path_resolver_test';

        if (!is_dir($this->tempProjectDir)) {
            mkdir($this->tempProjectDir, 0777, true);
        }
    }

    protected function tearDown(): void
    {
        $this->recursiveRemoveDir($this->tempProjectDir);
    }

    public function testGetTargetPathCreatesFolderAndReturnsCorrectPath(): void
    {
        $resolver = new StoragePathResolver($this->tempProjectDir);

        $filename = 'testfile.txt';
        $targetPath = $resolver->getTargetPath($filename);

        $expectedDir = $this->tempProjectDir . '/public/uploads/converted';
        $this->assertDirectoryExists($expectedDir, 'Upload folder moet aangemaakt worden');

        $this->assertStringEndsWith($expectedDir . '/' . $filename, $targetPath);
        $this->assertFileDoesNotExist($targetPath, 'Bestand bestaat nog niet');
    }

    private function recursiveRemoveDir(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = "$dir/$file";
            if (is_dir($path)) {
                $this->recursiveRemoveDir($path);
            } else {
                unlink($path);
            }
        }
        rmdir($dir);
    }
}
