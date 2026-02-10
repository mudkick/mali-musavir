<?php

namespace App\Services;

use App\Schemas\FaturaSchema;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Facades\Prism;
use Prism\Prism\ValueObjects\Media\Image;

class BelgeExtractionService
{
    /**
     * Extract fatura/makbuz data from image using Gemini Flash.
     */
    public function extract(string $imagePath): array
    {
        $image = Image::fromStoragePath($imagePath);

        $response = Prism::structured()
            ->using(Provider::Gemini, 'gemini-2.0-flash')
            ->withSystemPrompt(view('prompts.fatura-extraction'))
            ->withPrompt('Görseldeki fatura/makbuz bilgilerini extraction yap.', [$image])
            ->withSchema(FaturaSchema::schema())
            ->asStructured();

        return $response->structured;
    }

    /**
     * Validate extracted data with VKN checksum and amount cross-check.
     */
    public function validateExtraction(array $data): array
    {
        $errors = [];
        $validationPassed = true;

        // VKN validation
        if (isset($data['vkn'])) {
            if (! $this->isValidVkn($data['vkn'])) {
                $errors[] = 'VKN checksum geçersiz';
                $validationPassed = false;
            }
        }

        // Amount cross-validation
        if (isset($data['net_tutar'], $data['kdv_tutar'], $data['toplam'])) {
            $calculatedTotal = $data['net_tutar'] + $data['kdv_tutar'];
            $tolerance = abs($calculatedTotal * 0.02); // 2% tolerance
            $difference = abs($calculatedTotal - $data['toplam']);

            if ($difference > $tolerance) {
                $errors[] = sprintf(
                    'Tutar uyumsuzluğu: net (%s) + kdv (%s) = %s, toplam %s (fark: %s)',
                    number_format($data['net_tutar'], 2),
                    number_format($data['kdv_tutar'], 2),
                    number_format($calculatedTotal, 2),
                    number_format($data['toplam'], 2),
                    number_format($difference, 2)
                );
                $validationPassed = false;
            }
        }

        return [
            ...$data,
            'validation_passed' => $validationPassed,
            'validation_errors' => $errors,
        ];
    }

    /**
     * Validate Turkish VKN (Vergi Kimlik Numarası) checksum.
     */
    private function isValidVkn(string $vkn): bool
    {
        // Remove any non-digit characters
        $vkn = preg_replace('/[^0-9]/', '', $vkn);

        // Must be exactly 10 digits
        if (strlen($vkn) !== 10) {
            return false;
        }

        $digits = str_split($vkn);
        $sum = 0;

        // Calculate checksum for first 9 digits
        for ($i = 0; $i < 9; $i++) {
            $digit = (int) $digits[$i];
            $position = 9 - $i;

            // Step 1: (digit + position) % 10
            $temp = ($digit + $position) % 10;

            // Step 2: temp * 2^position
            $powerOfTwo = pow(2, $position);
            $value = ($temp * $powerOfTwo) % 9;

            // Step 3: If value is 0 and temp was not 0, use 9 instead
            if ($value === 0 && $temp !== 0) {
                $value = 9;
            }

            $sum += $value;
        }

        // Calculate expected check digit
        $expectedCheckDigit = (10 - ($sum % 10)) % 10;
        $actualCheckDigit = (int) $digits[9];

        return $expectedCheckDigit === $actualCheckDigit;
    }
}
