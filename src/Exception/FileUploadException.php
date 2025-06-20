<?php

namespace App\Exception;

use Psr\Log\LoggerInterface;

class FileUploadException
{
    public function __construct(
        private readonly LoggerInterface $logger
    )
    {
    }

    public function log(\Throwable $exception, ?string $context = null): void
    {
        $message = sprintf(
            "[%s] Error: %s\nBestand:%s on line %d\nTrace:\n%s",
            (new \DateTime())->format('Y-m-d H:i:s'),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getTraceAsString()
        );

        if ($context) {
            $message = "[$context] " . $message;
        }

        $this->logger->error($message);
    }
}
