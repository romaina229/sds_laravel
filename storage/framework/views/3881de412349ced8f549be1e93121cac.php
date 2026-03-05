<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de paiement – Shalom Digital Solutions</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica Neue', Arial, sans-serif; background: #f8fafc; color: #334155; line-height: 1.6; }
        .wrapper { max-width: 600px; margin: 0 auto; background: #ffffff; }
        .header { background: linear-gradient(135deg, #1e40af, #1e293b); padding: 40px 32px; text-align: center; }
        .header img { height: 48px; margin-bottom: 16px; }
        .header h1 { color: #ffffff; font-size: 22px; font-weight: 700; margin: 0; }
        .header p { color: #93c5fd; font-size: 14px; margin-top: 6px; }
        .badge { display: inline-block; background: #22c55e; color: #fff; font-weight: 700; font-size: 13px; padding: 6px 16px; border-radius: 20px; margin-top: 16px; }
        .body { padding: 40px 32px; }
        .greeting { font-size: 18px; font-weight: 600; color: #1e293b; margin-bottom: 12px; }
        .text { color: #64748b; font-size: 15px; margin-bottom: 24px; }
        .card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; margin: 24px 0; }
        .card-title { font-size: 13px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 16px; }
        .row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
        .row:last-child { border-bottom: none; }
        .row .label { color: #94a3b8; }
        .row .value { font-weight: 600; color: #1e293b; text-align: right; }
        .total-row { background: #eff6ff; border-radius: 8px; padding: 12px 16px; display: flex; justify-content: space-between; align-items: center; margin-top: 16px; }
        .total-row .label { font-weight: 700; color: #1e40af; }
        .total-row .value { font-size: 20px; font-weight: 900; color: #1e40af; }
        .steps { margin: 24px 0; }
        .step { display: flex; gap: 12px; margin-bottom: 16px; align-items: flex-start; }
        .step-num { width: 28px; height: 28px; background: #1e40af; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; flex-shrink: 0; }
        .step-text { padding-top: 4px; font-size: 14px; color: #475569; }
        .btn { display: inline-block; background: #1e40af; color: #ffffff !important; font-weight: 700; font-size: 15px; padding: 14px 32px; border-radius: 10px; text-decoration: none; margin: 8px 4px; }
        .btn-outline { background: transparent; border: 2px solid #1e40af; color: #1e40af !important; }
        .btn-center { text-align: center; margin: 24px 0; }
        .footer { background: #1e293b; padding: 24px 32px; text-align: center; }
        .footer p { color: #64748b; font-size: 12px; line-height: 1.8; }
        .footer a { color: #93c5fd; text-decoration: none; }
        .divider { border: none; border-top: 1px solid #e2e8f0; margin: 24px 0; }
    </style>
</head>
<body>
<div class="wrapper">

    <!-- Header -->
    <div class="header">
        <h1>🎉 Paiement confirmé !</h1>
        <p>Votre commande a été reçue et enregistrée</p>
        <div class="badge">✓ Commande <?php echo e($commande->numero_commande); ?></div>
    </div>

    <!-- Body -->
    <div class="body">
        <p class="greeting">Bonjour <?php echo e($commande->client_nom); ?>,</p>
        <p class="text">
            Nous avons bien reçu votre paiement. Notre équipe va prendre en charge votre projet dans les meilleurs délais.
            Vous recevrez un email de suivi dès le début des travaux.
        </p>

        <!-- Récap commande -->
        <div class="card">
            <div class="card-title">Récapitulatif de commande</div>
            <div class="row">
                <span class="label">Numéro</span>
                <span class="value"><?php echo e($commande->numero_commande); ?></span>
            </div>
            <div class="row">
                <span class="label">Service</span>
                <span class="value"><?php echo e($commande->service?->nom); ?></span>
            </div>
            <?php if($commande->service?->duree): ?>
            <div class="row">
                <span class="label">Durée estimée</span>
                <span class="value"><?php echo e($commande->service->duree); ?></span>
            </div>
            <?php endif; ?>
            <div class="row">
                <span class="label">Date de paiement</span>
                <span class="value"><?php echo e($commande->paiement_at?->format('d/m/Y à H:i')); ?></span>
            </div>
            <div class="row">
                <span class="label">Méthode de paiement</span>
                <span class="value"><?php echo e(ucfirst(str_replace('_', ' ', $commande->methode_paiement))); ?></span>
            </div>
            <hr class="divider">
            <div class="row">
                <span class="label">Montant HT</span>
                <span class="value"><?php echo e(number_format($commande->montant_fcfa, 0, ',', ' ')); ?> FCFA</span>
            </div>
            <div class="row">
                <span class="label">AIB (5%)</span>
                <span class="value"><?php echo e(number_format($commande->tva_fcfa, 0, ',', ' ')); ?> FCFA</span>
            </div>
            <div class="total-row">
                <span class="label">Total payé</span>
                <span class="value"><?php echo e(number_format($commande->total_ttc_fcfa, 0, ',', ' ')); ?> FCFA</span>
            </div>
        </div>

        <!-- Prochaines étapes -->
        <p style="font-size: 14px; font-weight: 700; color: #1e293b; margin-bottom: 16px;">Prochaines étapes :</p>
        <div class="steps">
            <div class="step">
                <div class="step-num">1</div>
                <div class="step-text"><strong>Analyse</strong> – Nous étudions votre demande et préparons votre projet (sous 24h ouvrées)</div>
            </div>
            <div class="step">
                <div class="step-num">2</div>
                <div class="step-text"><strong>Démarrage</strong> – Vous recevez un email de confirmation de démarrage avec le planning</div>
            </div>
            <div class="step">
                <div class="step-num">3</div>
                <div class="step-text"><strong>Livraison</strong> – Votre projet vous est livré dans les délais convenus avec un rapport complet</div>
            </div>
        </div>

        <!-- CTA -->
        <div class="btn-center">
            <a href="<?php echo e(config('app.url')); ?>/paiement/succes/<?php echo e($commande->numero_commande); ?>" class="btn">
                📄 Télécharger ma facture
            </a>
        </div>

        <hr class="divider">
        <p class="text" style="font-size: 13px;">
            Une question ? Contactez-nous directement :<br>
            📧 <a href="mailto:liferopro@gmail.com" style="color: #1e40af;">liferopro@gmail.com</a> &nbsp;·&nbsp;
            💬 <a href="https://wa.me/22994592567" style="color: #1e40af;">WhatsApp +229 01 94 59 25 67</a>
        </p>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>
            <strong style="color: #94a3b8;">Shalom Digital Solutions</strong><br>
            Abomey-Calavi, Bénin · <a href="mailto:liferopro@gmail.com">liferopro@gmail.com</a><br>
            <a href="<?php echo e(config('app.url')); ?>/mentions-legales">Mentions légales</a> ·
            <a href="<?php echo e(config('app.url')); ?>/confidentialite">Confidentialité</a>
        </p>
    </div>

</div>
</body>
</html>
<?php /**PATH D:\sds-backend\resources\views\emails\commande-confirmee.blade.php ENDPATH**/ ?>