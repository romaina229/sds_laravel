<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votre guide Finance Pro est disponible</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica Neue', Arial, sans-serif; background: #f8fafc; color: #334155; line-height: 1.6; }
        .wrapper { max-width: 600px; margin: 0 auto; background: #ffffff; }
        .header { background: linear-gradient(135deg, #1e40af, #1e293b); padding: 40px 32px; text-align: center; }
        .header h1 { color: #ffffff; font-size: 22px; font-weight: 700; margin: 0; }
        .header p { color: #93c5fd; font-size: 14px; margin-top: 6px; }
        .body { padding: 40px 32px; }
        .greeting { font-size: 18px; font-weight: 600; color: #1e293b; margin-bottom: 12px; }
        .text { color: #64748b; font-size: 15px; margin-bottom: 24px; }
        .btn { display: inline-block; background: #1e40af; color: #ffffff !important; font-weight: 700; font-size: 15px; padding: 16px 36px; border-radius: 10px; text-decoration: none; }
        .btn-center { text-align: center; margin: 28px 0; }
        .note { font-size: 12px; color: #94a3b8; text-align: center; margin-top: 8px; }
        .card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; margin: 28px 0; }
        .card-title { font-size: 13px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 12px; }
        .feature { display: flex; gap: 10px; margin-bottom: 10px; align-items: flex-start; font-size: 14px; color: #475569; }
        .feature-icon { color: #1e40af; font-weight: 700; }
        .divider { border: none; border-top: 1px solid #e2e8f0; margin: 28px 0; }
        .cta-secondary { text-align: center; }
        .btn-outline { display: inline-block; border: 2px solid #1e40af; color: #1e40af !important; font-weight: 700; font-size: 14px; padding: 12px 28px; border-radius: 10px; text-decoration: none; }
        .footer { background: #1e293b; padding: 24px 32px; text-align: center; }
        .footer p { color: #64748b; font-size: 12px; line-height: 1.8; }
        .footer a { color: #93c5fd; text-decoration: none; }
    </style>
</head>
<body>
<div class="wrapper">

    <div class="header">
        <h1>📘 Votre guide Finance Pro</h1>
        <p>Merci pour votre intérêt, {{ $guideDownload->organisation }} !</p>
    </div>

    <div class="body">
        <p class="greeting">Bonjour {{ $guideDownload->nom }},</p>
        <p class="text">
            Merci pour votre intérêt pour Finance Pro. Votre guide d'utilisation complet est prêt — cliquez ci-dessous pour le télécharger.
        </p>

        <div class="btn-center">
            <a href="{{ $downloadUrl }}" class="btn">Télécharger mon guide (PDF)</a>
            <p class="note">Ce lien est valable 48 heures.</p>
        </div>

        <div class="card">
            <div class="card-title">Ce que vous allez découvrir</div>
            <div class="feature"><span class="feature-icon">✓</span> Prise en main complète : projets, dépenses, recettes, budgets</div>
            <div class="feature"><span class="feature-icon">✓</span> Caisse, banque et suivi de trésorerie en temps réel</div>
            <div class="feature"><span class="feature-icon">✓</span> Génération de rapports financiers conformes SYSCOHADA</div>
            <div class="feature"><span class="feature-icon">✓</span> Gestion des équipes, rôles et double authentification</div>
        </div>

        <hr class="divider">

        <p class="text" style="margin-bottom: 16px;">
            Finance Pro est une solution SaaS développée par Shalom Digital Solutions, conçue pour la gestion financière des ONG, associations et projets de développement en Afrique de l'Ouest et Centrale.
        </p>

        <div class="cta-secondary">
            <a href="{{ config('app.frontend_url', 'https://shalomdigitalsolutions.com') }}/contact" class="btn-outline">Demander une démonstration</a>
        </div>
    </div>

    <div class="footer">
        <p>
            Shalom Digital Solutions — Abomey-Calavi, Bénin<br>
            <a href="mailto:afrisds@gmail.com">afrisds@gmail.com</a> · +229 01 44 95 83 83<br><br>
            Vous recevez cet email car vous avez demandé le guide Finance Pro sur notre site.
        </p>
    </div>

</div>
</body>
</html>
