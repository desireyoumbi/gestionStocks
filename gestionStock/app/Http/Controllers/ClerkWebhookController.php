<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Svix\Webhook;
use Svix\Exception\WebhookVerificationException;

class ClerkWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payloadRaw = $request->getContent();
        $headers = [
            'svix-id' => $request->header('svix-id'),
            'svix-timestamp' => $request->header('svix-timestamp'),
            'svix-signature' => $request->header('svix-signature'),
        ];

        $webhook = new Webhook(config('services.clerk.webhook_secret'));

        try {
            $payload = $webhook->verify($payloadRaw, $headers);
        } catch (WebhookVerificationException $e) {
            return response()->json(['error' => 'Signature invalide'], 401);
        }

        $eventType = $payload['type'];
        $userData = $payload['data'];

        if ($eventType === 'user.created') {
            User::create([
                'clerk_id' => $userData['id'],
                'email' => $userData['email_addresses'][0]['email_address'],
                'name' => trim($userData['first_name'] . ' ' . $userData['last_name']),
                'role' => 'vendeur',
            ]);
        }

        if ($eventType === 'user.updated') {
            User::where('clerk_id', $userData['id'])->update([
                'email' => $userData['email_addresses'][0]['email_address'],
                'name' => trim($userData['first_name'] . ' ' . $userData['last_name']),
            ]);
        }

        if ($eventType === 'user.deleted') {
            User::where('clerk_id', $userData['id'])->delete();
        }

        return response()->json(['status' => 'ok']);
    }
}