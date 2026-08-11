<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
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

        //appeller les methodes correspondandes

        return match ($eventType) {
            'user.created' => $this->createUser($userData),
            default => response()->json(['status' => 'ignored']),
        };

    
    }

    //creation d'un user

    private function createUser(array $userData){
        //transformer les infos envoyer par clerk plus comprehenssible

        $data = [
            'clerk_id' => $userData['id'] ?? null,
            'email' => $userData['email_addresses'][0]['email_address'] ?? null,
            'name' => trim(($userData['first_name'] ?? ''). '' . ($userData['last_name'] ?? '')),
            'phone' => $userData['private_metadata']['phone'] ?? null,
            'role' => $userData['private_metadata']['role'] ?? 'vendeur',
        ];

        // validation cote laravel
        $validator = Validator::make($data , [
            'clerk_id' => 'required|string|unique:users,clerk_id',
            'email' => 'required|email|unique:users,email',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:users,phone',
            'role' =>  'required|in:administrateur,vice_administrateur,vendeur'
        ]);


        // sa ne respecte pas les regles des tables
        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 422);
        }

        //valide les champ
        $validated = $validator->validated();

        // creation du user en db
        User::create($validated);
        // reponse vers le frontend
        return response()->json(['success' => true]);


    }

    

}