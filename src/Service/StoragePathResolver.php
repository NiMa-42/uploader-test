<?php

namespace App\Service;

class StoragePathResolver
{
    private string $targetDir;

    public function __construct(string $projectDir)
    {
        $this->targetDir = $projectDir . '/public/uploads/converted';
        $this->ensureDirectoryExists();
    }

    public function getTargetPath(string $filename): string
    {
        return $this->targetDir . '/' . $filename;
    }

    public function getPublicPath(string $filename): string
    {
        return '/uploads/converted/' . $filename;
    }

    private function ensureDirectoryExists(): void
    {
        if (!is_dir($this->targetDir)) {
            mkdir($this->targetDir, 0755, true);
        }
    }
}
