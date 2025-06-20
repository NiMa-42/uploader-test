<?php

namespace App\Tests\Service\Conversion\Strategy;

use App\Exception\FileConversionException;
use App\Service\Conversion\Strategy\XlsxToCsvConversion;
use App\Service\StoragePathResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class XlsxToCsvConversionTest extends TestCase
{
    private string $tempDir;
    private XlsxToCsvConversion $converter;
    private FileConversionException $loggerMock;
    private StoragePathResolver $pathResolver;

    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir() . '/conversion_test';

        $filesystem = new Filesystem();
        $filesystem->mkdir($this->tempDir);

        $this->pathResolver = new StoragePathResolver($this->tempDir);
        $this->loggerMock = $this->createMock(FileConversionException::class);
        $this->converter = new XlsxToCsvConversion($this->pathResolver, $this->loggerMock);
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->tempDir);
    }

    private function createXlsxTestFile(): UploadedFile
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'name');
        $sheet->setCellValue('B1', 'age');
        $sheet->setCellValue('A2', 'John');
        $sheet->setCellValue('B2', '30');
        $sheet->setCellValue('A3', 'Jane');
        $sheet->setCellValue('B3', '25');

        $filePath = $this->tempDir . '/test.xlsx';

        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        return new UploadedFile(
            $filePath,
            'test.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );
    }

    public function testConvertCreatesCsvFile(): void
    {
        $uploadedFile = $this->createXlsxTestFile();

        $outputPath = $this->converter->convert($uploadedFile);

        $this->assertFileExists($outputPath);
        $this->assertStringEndsWith('.csv', $outputPath);
        $this->assertStringContainsString($this->tempDir, $outputPath);
    }
}
