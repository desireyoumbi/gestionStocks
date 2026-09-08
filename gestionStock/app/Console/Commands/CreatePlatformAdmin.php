<?php

namespace App\Console\Commands;

use App\Models\PlatformAdmin;
use Illuminate\Console\Command;

class CreatePlatformAdmin extends Command
{
    protected $signature = 'platform:create-admin';

    protected $description = 'Créer un administrateur de la plateforme';

    public function handle(): int
    {
        $name = $this->ask('Nom de l’administrateur');
        $email = $this->ask('Email');
        $password = $this->secret('Mot de passe');
        $code = $this->secret('Code de connexion');

        if (PlatformAdmin::where('email', $email)->exists()) {
            $this->error('Un administrateur avec cet email existe déjà.');
            return self::FAILURE;
        }

        if (PlatformAdmin::where('code_connexion', $code)->exists()) {
            $this->error('Ce code de connexion existe déjà.');
            return self::FAILURE;
        }

        PlatformAdmin::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'code_connexion' => $code,
        ]);

        $this->newLine();
        $this->info('Administrateur créé avec succès.');
        $this->info("Email : {$email}");
        $this->info("Code de connexion : {$code}");

        return self::SUCCESS;
    }
}