<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Twilio\Rest\Client;

class WhatsAppService
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client(
            config('whatsapp.account_sid'),
            config('whatsapp.auth_token'),
        );
    }

    public function sendMessage(string $to, string $body): void
    {
        $this->client->messages->create(
            $this->formatPhoneNumber($to),
            [
                'from' => config('whatsapp.from_number'),
                'body' => $body,
            ],
        );
    }

    /**
     * Twilio Content Template ile butonlu mesaj gönder.
     */
    public function sendMessageWithButtons(string $to, string $body, string $contentSid): void
    {
        $this->client->messages->create(
            $this->formatPhoneNumber($to),
            [
                'from' => config('whatsapp.from_number'),
                'body' => $body,
                'contentSid' => $contentSid,
            ],
        );
    }

    /**
     * Twilio media URL'den dosya indirir ve storage'a kaydeder.
     */
    public function downloadMedia(string $mediaUrl): string
    {
        $response = Http::withBasicAuth(
            config('whatsapp.account_sid'),
            config('whatsapp.auth_token'),
        )->get($mediaUrl);

        $extension = match (true) {
            str_contains($response->header('Content-Type'), 'image/jpeg') => 'jpg',
            str_contains($response->header('Content-Type'), 'image/png') => 'png',
            str_contains($response->header('Content-Type'), 'application/pdf') => 'pdf',
            str_contains($response->header('Content-Type'), 'text/xml') => 'xml',
            default => 'bin',
        };

        $filename = 'belgeler/whatsapp/'.now()->format('Y-m-d').'/'.uniqid().'.'.$extension;

        Storage::disk('local')->put($filename, $response->body());

        return $filename;
    }

    /**
     * whatsapp:+90xxx formatını normalize eder.
     */
    public function formatPhoneNumber(string $number): string
    {
        $number = preg_replace('/[^0-9+]/', '', str_replace('whatsapp:', '', $number));

        if (str_starts_with($number, '0')) {
            $number = '+90'.substr($number, 1);
        }

        if (! str_starts_with($number, '+')) {
            $number = '+'.$number;
        }

        return 'whatsapp:'.$number;
    }

    /**
     * whatsapp:+90xxx → 05xxxxxxxxx formatına çevirir (DB eşleştirme için).
     */
    public function normalizeToLocal(string $twilioNumber): string
    {
        $number = str_replace('whatsapp:', '', $twilioNumber);
        $number = preg_replace('/[^0-9]/', '', $number);

        if (str_starts_with($number, '90') && strlen($number) === 12) {
            return '0'.substr($number, 2);
        }

        return $number;
    }
}
