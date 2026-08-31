<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $nouveauMotDePasse;

    public function __construct(User $user, string $nouveauMotDePasse)
    {
        $this->user = $user;
        $this->nouveauMotDePasse = $nouveauMotDePasse;
    }

    public function build()
    {
        return $this->subject('Réinitialisation de votre mot de passe - E-Stockly')
            ->view('emails.password-reset');
    }
}