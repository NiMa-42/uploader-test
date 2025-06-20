<?php

namespace App\Service\Conversion\Strategy;

use App\Exception\FileConversionException;
use App\Service\StoragePathResolver;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CsvToXlsxConversion implements FileConversionInterface
{
    public function __construct(
        private StoragePathResolver $pathResolver,
        private FileConversionException $exception
    ) {}

    public function supports(string $inputExtension, string $outputExtension): bool
    {
        return $inputExtension === 'csv' && $outputExtension === 'xlsx';
    }

    public function convert(UploadedFile $file): string
    {
        try {
            $reader = new Csv();
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getPathname());

            $convertedName = uniqid('converted_', true) . '.xlsx';
            $targetPath = $this->pathResolver->getTargetPath($convertedName);
            $writer = new Xlsx($spreadsheet);
            $writer->save($targetPath);

            return $targetPath;
        } catch (\Throwable $e) {
            $this->exception->log($e);
            throw $e;
        }
    }
}
