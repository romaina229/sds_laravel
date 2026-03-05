<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouveau message – <?php echo e($contact->sujet); ?></title>
    <style>
        body { font-family: Arial, sans-serif; background: #f8fafc; color: #334155; }
        .wrapper { max-width: 520px; margin: 32px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.08); }
        .header { background: #1e293b; padding: 24px 32px; }
        .header h1 { color: #fff; font-size: 18px; margin: 0; }
        .header p { color: #94a3b8; font-size: 13px; margin: 4px 0 0; }
        .badge { display: inline-block; background: #3b82f6; color: #fff; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 12px; margin-top: 10px; }
        .body { padding: 32px; }
        .field { margin-bottom: 16px; }
        .field-label { font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px; }
        .field-value { font-size: 15px; color: #1e293b; font-weight: 500; }
        .message-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; font-size: 14px; line-height: 1.7; color: #475569; white-space: pre-wrap; }
        .actions { margin-top: 24px; text-align: center; }
        .btn { display: inline-block; background: #1e40af; color: #fff !important; font-weight: 700; font-size: 14px; padding: 12px 24px; border-radius: 8px; text-decoration: none; margin: 4px; }
        .footer { background: #f8fafc; padding: 16px 32px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>📬 Nouveau message de contact</h1>
        <p>Reçu le <?php echo e(now()->format('d/m/Y à H:i')); ?></p>
        <div class="badge">Réf. <?php echo e($contact->reference); ?></div>
    </div>
    <div class="body">
        <div class="field">
            <div class="field-label">Expéditeur</div>
            <div class="field-value"><?php echo e($contact->nom); ?></div>
        </div>
        <div class="field">
            <div class="field-label">Email</div>
            <div class="field-value"><a href="mailto:<?php echo e($contact->email); ?>" style="color:#1e40af;"><?php echo e($contact->email); ?></a></div>
        </div>
        <?php if($contact->telephone): ?>
        <div class="field">
            <div class="field-label">Téléphone</div>
            <div class="field-value"><a href="tel:<?php echo e($contact->telephone); ?>" style="color:#1e40af;"><?php echo e($contact->telephone); ?></a></div>
        </div>
        <?php endif; ?>
        <?php if($contact->entreprise): ?>
        <div class="field">
            <div class="field-label">Organisation</div>
            <div class="field-value"><?php echo e($contact->entreprise); ?></div>
        </div>
        <?php endif; ?>
        <div class="field">
            <div class="field-label">Sujet</div>
            <div class="field-value"><?php echo e($contact->sujet); ?></div>
        </div>
        <div class="field">
            <div class="field-label">Message</div>
            <div class="message-box"><?php echo e($contact->message); ?></div>
        </div>
        <div class="actions">
            <a href="mailto:<?php echo e($contact->email); ?>?subject=Re: <?php echo e($contact->sujet); ?>&body=Bonjour <?php echo e($contact->nom); ?>,%0D%0A%0D%0A" class="btn">
                ✉️ Répondre
            </a>
            <a href="<?php echo e(config('app.url')); ?>/admin/contacts" class="btn" style="background:#475569;">
                Voir dans l'admin
            </a>
        </div>
    </div>
    <div class="footer">Shalom Digital Solutions · Abomey-Calavi, Bénin</div>
</div>
</body>
</html>
<?php /**PATH D:\sds-backend\resources\views\emails\nouveau-contact.blade.php ENDPATH**/ ?>