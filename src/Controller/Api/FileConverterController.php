<?php

namespace App\Controller\Api;

use App\Exception\FileUploadException;
use App\Service\Conversion\ConversionStrategyRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{BinaryFileResponse, JsonResponse, Request, Response, ResponseHeaderBag};
use Symfony\Component\Routing\Annotation\Route;

final class FileConverterController extends AbstractController
{
    public function __construct() {}

    #[Route('/api/convert', name: 'file_upload', methods: ['POST'])]
    public function upload(
        Request $request,
        ConversionStrategyRegistry $registry,
        FileUploadException $fileUploadException
    ): Response
    {
        try {
            $file = $request->files->get('file');

            if (!$file) {
                return new JsonResponse(['message' => 'Geen bestand geselecteerd', Response::HTTP_BAD_REQUEST]);
            }
        } catch (\Throwable $e) {
            $fileUploadException->log($e, 'File Upload');
            return new JsonResponse(['message' => 'Een onverwachte fout deed zich voort', Response::HTTP_BAD_REQUEST]);
        }

        try {
            $converted = $registry->convert($file);
            return new BinaryFileResponse($converted, 200, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . basename($converted) . '"',
            ]);
        } catch (\Throwable $e) {
            $fileUploadException->log($e, 'File Conversion');
            return new JsonResponse(['message', 'Er was een probleem bij het omzetten van het bestand', Response::HTTP_INTERNAL_SERVER_ERROR]);
        }
    }
}
