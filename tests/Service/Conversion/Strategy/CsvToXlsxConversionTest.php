<?php

namespace App\Tests\Service\Conversion\Strategy;

use App\Exception\FileConversionException;
use App\Service\Conversion\Strategy\CsvToXlsxConversion;
use App\Service\StoragePathResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CsvToXlsxConversionTest extends TestCase
{
    private string $tempDir;
    private CsvToXlsxConversion $converter;
    private FileConversionException $loggerMock;
    private StoragePathResolver $pathResolver;

    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir() . '/conversion_test';

        $filesystem = new Filesystem();
        $filesystem->mkdir($this->tempDir);

        $this->pathResolver = new StoragePathResolver($this->tempDir);

        $this->loggerMock = $this->createMock(FileConversionException::class);

        $this->converter = new CsvToXlsxConversion($this->pathResolver, $this->loggerMock);
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->tempDir);
    }

    private function createTestFile(): UploadedFile
    {
        $csvContent = "name,age\nJohn,30\nJane,25\n";

        $csvFile = $this->tempDir . '/test.csv';
        file_put_contents($csvFile, $csvContent);

        return new UploadedFile(
            $csvFile,
            'test.csv',
            'text/csv',
            null,
            true
        );
    }

    public function testConvertCreatesXlsxFile(): void
    {
        $uploadedFile = $this->createTestFile();

        $outputPath = $this->converter->convert($uploadedFile);

        $this->assertFileExists($outputPath);
        $this->assertStringEndsWith('.xlsx', $outputPath);
        $this->assertStringContainsString($this->tempDir, $outputPath);
    }
}
