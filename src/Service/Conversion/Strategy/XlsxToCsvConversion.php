<?php

namespace App\Service\Conversion\Strategy;

use App\Exception\FileConversionException;
use App\Service\StoragePathResolver;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class XlsxToCsvConversion implements FileConversionInterface
{
    public function __construct(
        private StoragePathResolver $pathResolver,
        private FileConversionException $exception
    ) {}

    public function supports(string $inputExtension, string $outputExtension): bool
    {
        return $inputExtension === 'xlsx' && $outputExtension === 'csv';
    }

    public function convert(UploadedFile $file): string
    {
        try {
            $reader = new Xlsx();
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getPathname());

            $convertedName = uniqid('converted_', true) . '.csv';
            $targetPath = $this->pathResolver->getTargetPath($convertedName);

            $writer = new Csv($spreadsheet);
            $writer->save($targetPath);

            return $targetPath;
        }  catch (\Throwable $e) {
            $this->exception->log($e);
        }

    }
}
