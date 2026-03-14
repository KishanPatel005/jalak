<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $baseUrl;
    protected $apiKey;
    protected $instance;
    protected $adminNumber;

    public function __construct()
    {
        $this->baseUrl = config('services.whatsapp.base_url');
        $this->apiKey = config('services.whatsapp.api_key');
        $this->instance = config('services.whatsapp.instance');
        $this->adminNumber = config('services.whatsapp.admin_number');
    }

    public function getAdminNumber()
    {
        return $this->adminNumber;
    }

    /**
     * Send only text message
     * 
     * @param string $number
     * @param string $text
     * @return array
     */
    public function sendText($number, $text)
    {
        $url = "{$this->baseUrl}/message/sendText/{$this->instance}";
        
        try {
            Log::info("WhatsApp Sending to {$number} via {$url}");
            $response = Http::withHeaders([
                'apikey' => $this->apiKey,
                'Content-Type' => 'application/json'
            ])
            ->withoutVerifying()
            ->post($url, [
                'number' => $this->formatNumber($number),
                'text' => $text
            ]);

            Log::info("WhatsApp Response: " . $response->body());
            return $response->json();
        } catch (\Exception $e) {
            Log::error("WhatsApp sendText Error: " . $e->getMessage());
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    /**
     * Send PDF document
     * 
     * @param string $number
     * @param string $mediaUrl Publicly accessible URL to the PDF
     * @param string $fileName FileName shown in WhatsApp
     * @param string|null $caption Optional message with the PDF
     * @return array
     */
    public function sendPdfUrl($number, $mediaUrl, $fileName, $caption = null)
    {
        $url = "{$this->baseUrl}/message/sendMedia/{$this->instance}";
        
        $payload = [
            'number' => $this->formatNumber($number),
            'media' => $mediaUrl,
            'mediatype' => 'document',
            'mimetype' => 'application/pdf',
            'fileName' => $fileName
        ];

        if ($caption) {
            $payload['caption'] = $caption;
        }

        try {
            Log::info("WhatsApp Media Sending to {$number} URL: {$mediaUrl}");
            $response = Http::withHeaders([
                'apikey' => $this->apiKey,
                'Content-Type' => 'application/json'
            ])
            ->withoutVerifying()
            ->post($url, $payload);

            Log::info("WhatsApp Media Response: " . $response->body());
            return $response->json();
        } catch (\Exception $e) {
            Log::error("WhatsApp sendPdf Error: " . $e->getMessage());
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    /**
     * Higher level function: Send PDF by providing raw PDF content
     * 
     * @param string $number
     * @param string $pdfContent Raw PDF data
     * @param string $fileName
     * @param string|null $caption
     * @return array
     */
    public function sendPdfContent($number, $pdfContent, $fileName, $caption = null)
    {
        // Encode the PDF as raw base64 (NO data: prefix) and send directly via Evolution API.
        // This avoids the "fetch failed" error caused by Evolution API trying to download
        // from an HTTPS URL with an untrusted/self-signed certificate.
        $base64 = base64_encode($pdfContent);

        $url = "{$this->baseUrl}/message/sendMedia/{$this->instance}";

        $payload = [
            'number'    => $this->formatNumber($number),
            'media'     => $base64,
            'mediatype' => 'document',
            'mimetype'  => 'application/pdf',
            'fileName'  => $fileName,
        ];

        if ($caption) {
            $payload['caption'] = $caption;
        }

        try {
            Log::info("WhatsApp Base64 PDF Sending to {$number}, file: {$fileName}");
            $response = Http::withHeaders([
                'apikey'       => $this->apiKey,
                'Content-Type' => 'application/json',
            ])
            ->withoutVerifying()
            ->timeout(60)
            ->post($url, $payload);

            Log::info("WhatsApp Base64 PDF Response: " . $response->body());
            return $response->json();
        } catch (\Exception $e) {
            Log::error("WhatsApp sendPdfContent Error: " . $e->getMessage());
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    /**
     * Handle number formatting
     */
    protected function formatNumber($number)
    {
        // Remove +, spaces, dashes
        $number = preg_replace('/[^0-9]/', '', $number);
        
        // Logic to ensure country code (e.g. 91 for India)
        // If it starts with 0, replace with 91
        if (strlen($number) === 10) {
            $number = '91' . $number;
        }
        
        return $number;
    }
}
