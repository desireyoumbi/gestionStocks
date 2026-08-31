<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
</head>
<body style="font-family: Arial, sans-serif; background-color: #f8fafc; padding: 30px 0; margin: 0;">
    <div style="max-width: 480px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <div style="background-color: #f59e0b; padding: 24px; text-align: center;">
            <h1 style="color: #111827; margin: 0; font-size: 20px;">E-Stockly</h1>
        </div>
        <div style="padding: 30px 28px;">
            <h2 style="color: #111827; font-size: 18px; margin-top: 0;">Bonjour {{ $user->name }},</h2>
            <p style="color: #475569; font-size: 14px; line-height: 1.6;">
                Vous avez demandé la réinitialisation de votre mot de passe pour votre compte
                <strong>{{ $user->boutique->nom ?? 'E-Stockly' }}</strong>.
            </p>

            <p style="color: #475569; font-size: 14px; line-height: 1.6;">
                Voici votre code de connexion et votre mot de passe temporaire :
            </p>

            <div style="background: #f1f5f9; border-radius: 12px; padding: 16px 20px; margin: 20px 0;">
                <p style="margin: 0 0 8px 0; font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: bold;">Code de connexion</p>
                <p style="margin: 0 0 16px 0; font-size: 18px; font-weight: bold; letter-spacing: 1px; color: #111827;">{{ $user->code_connexion }}</p>

                <p style="margin: 0 0 8px 0; font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: bold;">Mot de passe temporaire</p>
                <p style="margin: 0; font-size: 18px; font-weight: bold; letter-spacing: 1px; color: #111827;">{{ $nouveauMotDePasse }}</p>
            </div>

            <p style="color: #475569; font-size: 13px; line-height: 1.6;">
                ⚠️ Pour votre sécurité, il vous sera demandé de choisir un nouveau mot de passe dès votre prochaine connexion.
            </p>

            <p style="color: #94a3b8; font-size: 12px; margin-top: 24px;">
                Si vous n'êtes pas à l'origine de cette demande, contactez immédiatement votre administrateur.
            </p>
        </div>
    </div>
</body>
</html>