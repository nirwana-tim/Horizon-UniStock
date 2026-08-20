<?php

namespace App\Services\Master;

use App\Models\StudentGeneration;

class GenerationResolverService
{
    public function resolveFromNim(?string $nim): ?StudentGeneration
    {
        if (! $nim) {
            return null;
        }

        $code = $this->parseCodeFromNim($nim);
        if (! $code) {
            return null;
        }

        $generation = StudentGeneration::where('code', $code)->first();
        if (! $generation) {
            $yearPrefix = '20'.substr($code, 0, 2);
            $yearSuffix = '20'.substr($code, 2, 2);
            $generation = StudentGeneration::create([
                'code' => $code,
                'name' => $yearPrefix.'/'.$yearSuffix,
            ]);
        }

        return $generation;
    }

    public function parseCodeFromNim(string $nim): ?string
    {
        $nim = trim($nim);

        if (strlen($nim) >= 12 && ctype_digit($nim)) {
            $year = (int) substr($nim, 10, 2);

            return sprintf('%02d%02d', $year, $year + 1);
        }

        if (preg_match('/^20(\d{2})/', $nim, $matches)) {
            $year = (int) $matches[1];

            return sprintf('%02d%02d', $year, $year + 1);
        }

        if (preg_match('/^(\d{2})\d+$/', $nim, $matches)) {
            $year = (int) $matches[1];

            return sprintf('%02d%02d', $year, $year + 1);
        }

        return null;
    }
}
