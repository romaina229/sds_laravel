<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouveau téléchargement du guide Finance Pro</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f8fafc; color: #334155; }
        .wrapper { max-width: 520px; margin: 32px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.08); }
        .header { background: #1e293b; padding: 24px 32px; }
        .header h1 { color: #fff; font-size: 18px; margin: 0; }
        .header p { color: #94a3b8; font-size: 13px; margin: 4px 0 0; }
        .badge { display: inline-block; background: #22c55e; color: #fff; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 12px; margin-top: 10px; }
        .body { padding: 32px; }
        .field { margin-bottom: 16px; }
        .field-label { font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px; }
        .field-value { font-size: 15px; color: #1e293b; font-weight: 500; }
        .grid { display: flex; flex-wrap: wrap; gap: 16px; }
        .grid .field { flex: 1 1 45%; min-width: 140px; }
        .consent { margin-top: 8px; font-size: 13px; padding: 10px 14px; border-radius: 8px; }
        .consent-yes { background: #f0fdf4; color: #15803d; }
        .consent-no { background: #fef2f2; color: #b91c1c; }
        .actions { margin-top: 24px; text-align: center; }
        .btn { display: inline-block; background: #1e40af; color: #fff !important; font-weight: 700; font-size: 14px; padding: 12px 24px; border-radius: 8px; text-decoration: none; margin: 4px; }
        .footer { background: #f8fafc; padding: 16px 32px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>📥 Nouveau prospect qualifié — Finance Pro</h1>
        <p>Formulaire de téléchargement du guide, reçu le <?php echo e($guideDownload->created_at->format('d/m/Y à H:i')); ?></p>
        <div class="badge">Prospect #<?php echo e($guideDownload->id); ?></div>
    </div>
    <div class="body">
        <div class="grid">
            <div class="field">
                <div class="field-label">Organisation</div>
                <div class="field-value"><?php echo e($guideDownload->organisation); ?></div>
            </div>
            <div class="field">
                <div class="field-label">Responsabilité</div>
                <div class="field-value"><?php echo e($guideDownload->fonctionLabel()); ?></div>
            </div>
            <div class="field">
                <div class="field-label">Pays</div>
                <div class="field-value"><?php echo e($guideDownload->pays); ?></div>
            </div>
            <?php if($guideDownload->taille_organisation): ?>
            <div class="field">
                <div class="field-label">Taille de l'organisation</div>
                <div class="field-value"><?php echo e(\App\Models\GuideDownload::TAILLES[$guideDownload->taille_organisation] ?? $guideDownload->taille_organisation); ?></div>
            </div>
            <?php endif; ?>
            <?php if($guideDownload->nombre_projets): ?>
            <div class="field">
                <div class="field-label">Projets gérés actuellement</div>
                <div class="field-value"><?php echo e($guideDownload->nombre_projets); ?></div>
            </div>
            <?php endif; ?>
        </div>

        <div class="field">
            <div class="field-label">Contact</div>
            <div class="field-value"><?php echo e($guideDownload->nom); ?></div>
            <div class="field-value"><a href="mailto:<?php echo e($guideDownload->email); ?>" style="color:#1e40af;"><?php echo e($guideDownload->email); ?></a></div>
            <?php if($guideDownload->telephone): ?>
            <div class="field-value"><a href="tel:<?php echo e($guideDownload->telephone); ?>" style="color:#1e40af;"><?php echo e($guideDownload->telephone); ?></a></div>
            <?php endif; ?>
        </div>

        <div class="consent <?php echo e($guideDownload->consentement_marketing ? 'consent-yes' : 'consent-no'); ?>">
            <?php echo e($guideDownload->consentement_marketing ? '✓ A accepté d\'être contacté(e) au sujet de Finance Pro.' : '✗ N\'a PAS souhaité être contacté(e) — respecter ce choix pour toute relance commerciale.'); ?>

        </div>

        <div class="actions">
            <a href="mailto:<?php echo e($guideDownload->email); ?>?subject=Finance Pro — suite à votre téléchargement du guide" class="btn">✉️ Relancer par email</a>
        </div>
    </div>
    <div class="footer">
        Shalom Digital Solutions — Notification automatique, Admin &gt; Guide Finance Pro.
    </div>
</div>
</body>
</html>
<?php /**PATH D:\ShalomDigitalSolutions\sds-backend\resources\views\emails\nouveau-guide-download.blade.php ENDPATH**/ ?>