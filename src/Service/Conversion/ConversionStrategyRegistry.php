<?php

namespace App\Service\Conversion;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class ConversionStrategyRegistry
{
    /** @var iterable<ConversionStrategyRegistry> */
    private iterable $strategies;

    public function __construct(iterable $strategies)
    {
        $this->strategies = $strategies;
    }

    public function convert(UploadedFile $file): string
    {
        $input = strtolower($file->getClientOriginalExtension());
        $output = $input === 'csv' ? 'xlsx' : ($input === 'xlsx' ? 'csv' : null);

        if (!$output) {
            throw new \InvalidArgumentException("Geen geldige conversie voor extensie '$input'");
        }

        if (!file_exists($file->getPathname())) {
            throw new \RuntimeException('Bestand bestaat niet: ' . $file->getPathname());
        }

        foreach ($this->strategies as $strategy) {
            if ($strategy->supports($input, $output)) {
                return $strategy->convert($file);
            }
        }

        throw new \RuntimeException("Geen geschikte strategie gevonden voor $input → $output.");
    }
}
