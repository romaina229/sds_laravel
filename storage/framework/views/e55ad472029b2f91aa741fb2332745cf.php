<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture <?php echo e($facture->numero_facture); ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; color: #333; font-size: 12px; }
        .container { padding: 40px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 40px; border-bottom: 3px solid #3498db; padding-bottom: 20px; }
        .logo-section h1 { color: #2c3e50; font-size: 22px; font-weight: 900; }
        .logo-section h1 span { color: #3498db; }
        .logo-section p { color: #666; font-size: 11px; margin-top: 5px; }
        .invoice-info { text-align: right; }
        .invoice-info h2 { color: #3498db; font-size: 28px; font-weight: 900; text-transform: uppercase; letter-spacing: 2px; }
        .invoice-info p { color: #666; font-size: 11px; }
        .invoice-number { color: #2c3e50; font-weight: bold; font-size: 14px; }
        
        .parties { display: flex; justify-content: space-between; margin-bottom: 30px; gap: 30px; }
        .party { flex: 1; }
        .party h3 { background: #3498db; color: white; padding: 8px 12px; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; }
        .party p { padding: 2px 0; color: #555; line-height: 1.6; }
        .party strong { color: #2c3e50; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        thead th { background: #2c3e50; color: white; padding: 10px 12px; text-align: left; font-size: 11px; text-transform: uppercase; }
        tbody td { padding: 12px; border-bottom: 1px solid #eee; font-size: 12px; }
        tbody tr:nth-child(even) { background: #f8f9fa; }
        tfoot td { padding: 8px 12px; font-weight: bold; }
        .total-row { background: #3498db !important; color: white; }
        .total-row td { color: white; font-size: 14px; }
        
        .footer { margin-top: 40px; padding-top: 20px; border-top: 1px solid #eee; text-align: center; color: #999; font-size: 10px; }
        .status-badge { display: inline-block; background: #27ae60; color: white; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; }
        .aib-note { background: #fff3cd; padding: 8px 12px; border-left: 4px solid #f39c12; font-size: 11px; color: #856404; margin-bottom: 20px; }
    </style>
</head>
<body>
<div class="container">
    <!-- En-tête -->
    <div class="header">
        <div class="logo-section">
            <h1>Shalom Digital <span>Solutions</span></h1>
            <p>Solutions Numériques Complètes</p>
            <p>Abomey-Calavi, Bénin</p>
            <p>+229 01 69 35 17 66 | liferopro@gmail.com</p>
        </div>
        <div class="invoice-info">
            <h2>Facture</h2>
            <p class="invoice-number"><?php echo e($facture->numero_facture); ?></p>
            <p>Date : <?php echo e($facture->created_at->format('d/m/Y')); ?></p>
            <p>Commande : <?php echo e($commande->numero_commande); ?></p>
            <p>Statut : <span class="status-badge">PAYÉE</span></p>
        </div>
    </div>

    <!-- Parties -->
    <div class="parties">
        <div class="party">
            <h3>Émetteur</h3>
            <p><strong>Shalom Digital Solutions</strong></p>
            <p>Abomey-Calavi, Bénin</p>
            <p>+229 01 69 35 17 66</p>
            <p>liferopro@gmail.com</p>
            <p>www.shalomdigitalsolutions.com</p>
        </div>
        <div class="party">
            <h3>Client</h3>
            <p><strong><?php echo e($facture->client_nom); ?></strong></p>
            <?php if($facture->client_entreprise): ?>
            <p><?php echo e($facture->client_entreprise); ?></p>
            <?php endif; ?>
            <p><?php echo e($facture->client_email); ?></p>
            <?php if($facture->client_telephone): ?>
            <p><?php echo e($facture->client_telephone); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Détails service -->
    <table>
        <thead>
            <tr>
                <th>Désignation</th>
                <th>Durée</th>
                <th style="text-align:right">Prix HT (FCFA)</th>
                <th style="text-align:right">Prix HT (€)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong><?php echo e($commande->service_nom); ?></strong><br>
                    <small style="color:#666">Commande N° <?php echo e($commande->numero_commande); ?></small>
                </td>
                <td><?php echo e($commande->duree_estimee ?? '-'); ?></td>
                <td style="text-align:right"><?php echo e(number_format($commande->montant_fcfa, 0, ',', ' ')); ?> FCFA</td>
                <td style="text-align:right"><?php echo e(number_format($commande->montant_euro, 2, ',', ' ')); ?> €</td>
            </tr>
        </tbody>
        <tfoot>
            <tr style="background:#f8f9fa">
                <td colspan="2" style="text-align:right">Montant HT :</td>
                <td colspan="2" style="text-align:right"><?php echo e(number_format($commande->montant_fcfa, 0, ',', ' ')); ?> FCFA</td>
            </tr>
            <tr style="background:#f8f9fa">
                <td colspan="2" style="text-align:right">AIB (<?php echo e($taux_aib * 100); ?>%) :</td>
                <td colspan="2" style="text-align:right"><?php echo e(number_format($commande->tva_fcfa, 0, ',', ' ')); ?> FCFA</td>
            </tr>
            <tr class="total-row">
                <td colspan="2" style="text-align:right;font-size:14px;">TOTAL TTC :</td>
                <td colspan="2" style="text-align:right;font-size:16px;font-weight:900;"><?php echo e(number_format($commande->total_ttc_fcfa, 0, ',', ' ')); ?> FCFA</td>
            </tr>
        </tfoot>
    </table>

    <div class="aib-note">
        <strong>Note fiscale :</strong> AIB (Acompte sur Impôt sur les Bénéfices) de <?php echo e($taux_aib * 100); ?>% appliquée conformément à la législation fiscale du Bénin.
    </div>

    <!-- Paiement info -->
    <p style="margin-bottom:5px;font-size:11px;color:#555;">
        <strong>Méthode de paiement :</strong> <?php echo e($commande->methode_paiement_label); ?>

        <?php if($commande->paiement_at): ?>
        | <strong>Date de paiement :</strong> <?php echo e($commande->paiement_at->format('d/m/Y à H:i')); ?>

        <?php endif; ?>
    </p>

    <!-- Footer -->
    <div class="footer">
        <p>Merci pour votre confiance ! | Shalom Digital Solutions - <?php echo e(date('Y')); ?></p>
        <p>Ce document est une facture officielle. Conservez-la pour vos archives.</p>
    </div>
</div>
</body>
</html>
<?php /**PATH D:\sds-backend\resources\views\pdf\facture.blade.php ENDPATH**/ ?>