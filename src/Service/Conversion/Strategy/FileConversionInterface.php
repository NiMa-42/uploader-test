<?php

namespace App\Service\Conversion\Strategy;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface FileConversionInterface
{
    public function supports(string $inputExtension, string $outputExtension): bool;

    public function convert(UploadedFile $file): string;
}
