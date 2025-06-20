<?php

namespace App\Tests\Controller;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileConverterControllerTest extends WebTestCase
{
    private string $tempDir;

    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir() . '/api_convert_test';

        if (!is_dir($this->tempDir)) {
            mkdir($this->tempDir, 0777, true);
        }
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->tempDir);
    }

    private function createValidXlsxFile(): UploadedFile
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Name');
        $sheet->setCellValue('B1', 'Age');
        $sheet->setCellValue('A2', 'Alice');
        $sheet->setCellValue('B2', '30');

        $path = $this->tempDir . '/test.xlsx';
        (new Xlsx($spreadsheet))->save($path);

        return new UploadedFile($path, 'test.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);
    }

    public function testConvertEndpointReturnsConvertedPath(): void
    {
        $client = static::createClient();
        $uploadedFile = $this->createValidXlsxFile();

        $client->request(
            'POST',
            '/api/convert',
            [],
            ['file' => $uploadedFile],
            ['CONTENT_TYPE' => 'multipart/form-data']
        );

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        $this->assertTrue(
            $response->headers->contains('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            || $response->headers->contains('Content-Type', 'text/csv'),
            'Content-Type header moet geldig zijn'
        );

        $this->assertStringContainsString(
            'attachment;',
            $response->headers->get('Content-Disposition')
        );
    }
}
