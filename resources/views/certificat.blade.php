<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
        font-family: 'DejaVu Sans', sans-serif;
        background: #fff;
        width: 842px; height: 595px; /* A4 paysage */
        overflow: hidden;
    }

    /* Bordure décorative */
    .border-outer {
        position: absolute; inset: 10px;
        border: 3px solid #1e40af;
        border-radius: 8px;
    }
    .border-inner {
        position: absolute; inset: 16px;
        border: 1px solid #93c5fd;
        border-radius: 6px;
    }

    /* Bandes latérales */
    .band-left {
        position: absolute; left: 0; top: 0; bottom: 0;
        width: 70px;
        background: linear-gradient(180deg, #1e40af 0%, #3b82f6 100%);
        border-radius: 8px 0 0 8px;
    }
    .band-right {
        position: absolute; right: 0; top: 0; bottom: 0;
        width: 70px;
        background: linear-gradient(180deg, #1e40af 0%, #3b82f6 100%);
        border-radius: 0 8px 8px 0;
    }
    .band-text {
        position: absolute;
        color: white;
        font-size: 11px;
        letter-spacing: 3px;
        text-transform: uppercase;
        font-weight: bold;
        white-space: nowrap;
    }
    .band-text-left {
        left: 18px; top: 50%;
        transform: rotate(-90deg) translateX(-50%);
        transform-origin: left center;
    }
    .band-text-right {
        right: 18px; top: 50%;
        transform: rotate(90deg) translateX(50%);
        transform-origin: right center;
    }

    /* Contenu central */
    .content {
        position: absolute;
        left: 90px; right: 90px;
        top: 30px; bottom: 30px;
        text-align: center;
    }

    /* Logo / Org */
    .org-name {
        font-size: 13px;
        color: #3b82f6;
        font-weight: bold;
        letter-spacing: 2px;
        text-transform: uppercase;
        margin-bottom: 4px;
    }
    .divider {
        width: 60px; height: 2px;
        background: #3b82f6;
        margin: 6px auto;
    }

    /* Titre */
    .title {
        font-size: 32px;
        color: #1e293b;
        font-weight: bold;
        letter-spacing: 4px;
        text-transform: uppercase;
        margin: 10px 0 4px;
    }
    .subtitle {
        font-size: 13px;
        color: #64748b;
        letter-spacing: 1px;
        margin-bottom: 14px;
    }

    /* Nom participant */
    .certifie-label {
        font-size: 12px;
        color: #64748b;
        margin-bottom: 4px;
    }
    .participant-name {
        font-size: 28px;
        color: #1e40af;
        font-weight: bold;
        font-style: italic;
        border-bottom: 2px solid #bfdbfe;
        padding-bottom: 6px;
        margin: 0 40px 10px;
    }

    /* Corps texte */
    .body-text {
        font-size: 12px;
        color: #475569;
        line-height: 1.7;
        margin-bottom: 10px;
    }
    .body-text strong { color: #1e293b; }

    /* Mention */
    .mention-badge {
        display: inline-block;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        color: #1e40af;
        font-size: 11px;
        font-weight: bold;
        padding: 3px 14px;
        border-radius: 20px;
        margin-bottom: 14px;
    }

    /* Footer */
    .footer {
        position: absolute;
        bottom: 0; left: 0; right: 0;
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
    }
    .signature-block {
        text-align: center;
        flex: 1;
    }
    .signature-line {
        width: 120px;
        border-top: 1px solid #94a3b8;
        margin: 0 auto 4px;
    }
    .signature-label {
        font-size: 10px;
        color: #64748b;
    }
    .qr-block {
        text-align: center;
        flex: 0 0 80px;
    }
    .qr-placeholder {
        width: 60px; height: 60px;
        border: 1px solid #e2e8f0;
        margin: 0 auto 3px;
        display: flex; align-items: center; justify-content: center;
        font-size: 8px; color: #94a3b8;
        background: #f8fafc;
    }
    .qr-label { font-size: 8px; color: #94a3b8; }
    .code-verification { font-size: 7px; color: #cbd5e1; margin-top: 2px; }

    /* Étoiles décoratives */
    .stars { color: #fbbf24; font-size: 14px; margin-bottom: 6px; }
</style>
</head>
<body>

<div class="band-left">
    <span class="band-text band-text-left">{{ $organisation }}</span>
</div>
<div class="band-right">
    <span class="band-text band-text-right">{{ date('Y') }}</span>
</div>
<div class="border-outer"></div>
<div class="border-inner"></div>

<div class="content">
    <div class="org-name">{{ $organisation }}</div>
    <div class="divider"></div>

    <div class="title">Certificat</div>
    <div class="subtitle">de participation &amp; de compétence</div>

    <div class="certifie-label">Décerné à</div>
    <div class="participant-name">{{ $nom_complet }}</div>

    <div class="body-text">
        pour avoir suivi avec succès la formation<br>
        <strong>« {{ $formation }} »</strong><br>
        @if($duree)
            d'une durée de <strong>{{ $duree }}</strong> —
        @endif
        le <strong>{{ \Carbon\Carbon::parse($date_formation)->translatedFormat('d F Y') }}</strong>
    </div>

    @if($mention)
        <div class="mention-badge">Mention : {{ $mention }}</div>
    @endif

    <div class="stars">★ ★ ★</div>

    <div class="footer">
        <div class="signature-block">
            <div class="signature-line"></div>
            <div class="signature-label">Le Directeur</div>
            <div class="signature-label" style="font-weight:bold;color:#1e40af;">Shalom Digital Solutions</div>
        </div>

        <div class="qr-block">
            <div class="qr-placeholder">QR</div>
            <div class="qr-label">Vérifier</div>
            <div class="code-verification">{{ $code_verification }}</div>
        </div>
    </div>
</div>

</body>
</html>
