<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BrevoMailer
{
    public static function envoyer(string $destinataireEmail, string $destinataireNom, string $sujet, string $contenuHtml): bool
    {
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'api-key' => config('services.brevo.api_key'),
            'content-type' => 'application/json',
        ])->post('https://api.brevo.com/v3/smtp/email', [
            'sender' => [
                'name' => 'E-Stockly',
                'email' => config('services.brevo.sender_email'),
            ],
            'to' => [
                [
                    'email' => $destinataireEmail,
                    'name' => $destinataireNom,
                ],
            ],
            'subject' => $sujet,
            'htmlContent' => $contenuHtml,
        ]);

        if ($response->failed()) {
            Log::error('Échec envoi email via Brevo API', [
                'destinataire' => $destinataireEmail,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return false;
        }

        return true;
    }
}