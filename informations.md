# 🚀 Shalom Digital Solutions — Laravel + React

Application web moderne, sécurisée et performante.

---

## 🏗 Stack Technique

| Couche | Technologie |
|---|---|
| Backend | **Laravel 11** (API REST) |
| Frontend | **React 18** + **React Router 6** |
| CSS | **Tailwind CSS 3** |
| Base de données | **MySQL** |
| Auth API | **Laravel Sanctum** (tokens) |
| Paiements | **FedaPay** (carte/virement) + **CinetPay** (Mobile Money) |
| PDF | **DomPDF** (factures) |
| Build | **Vite 5** |

---

## 📦 Installation

### Prérequis
- PHP ≥ 8.2
- Node.js ≥ 20
- MySQL 8+
- Composer 2+

### 1. Cloner et configurer

```bash
# Dépendances PHP
composer install

# Copier et configurer .env
cp .env.example .env
php artisan key:generate

# Configurer la base de données dans .env
# DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD

# Migrations et seeders
php artisan migrate --seed

# Lien storage
php artisan storage:link
```

### 2. Dépendances JS

```bash
npm install
```

### 3. Développement

```bash
# Terminal 1 : Laravel
php artisan serve

# Terminal 2 : Vite
npm run dev
```

### 4. Production

```bash
# Build React
npm run build

# Cache Laravel
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 💳 Configuration des Paiements

### FedaPay (Carte bancaire + Virement)

1. Créer un compte sur [fedapay.com](https://fedapay.com)
2. Récupérer vos clés API (sandbox → live)
3. Renseigner dans `.env` :
```
FEDAPAY_PUBLIC_KEY=pk_live_xxx
FEDAPAY_SECRET_KEY=sk_live_xxx
FEDAPAY_ENVIRONMENT=live
```
4. Configurer le webhook FedaPay → `https://votredomaine.com/api/paiement/callback/fedapay`

### CinetPay (Mobile Money : Orange, MTN, Moov)

1. Créer un compte sur [cinetpay.com](https://cinetpay.com)
2. Récupérer API Key et Site ID
3. Renseigner dans `.env` :
```
CINETPAY_API_KEY=xxxx
CINETPAY_SITE_ID=xxxx
```
4. Configurer le webhook CinetPay → `https://votredomaine.com/api/paiement/callback/cinetpay`

> **Note** : Les clés peuvent aussi être configurées dans l'admin via Paramètres.

---

## 👤 Connexion Admin

- URL : `/admin`
- Email par défaut : `admin@shalomdigitalsolutions.com`
- Mot de passe : `Shalom@Admin2026!`

> ⚠️ **Changer le mot de passe immédiatement en production !**

---

## 📁 Structure du Projet

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Api/           # Contrôleurs API publics
│   │   ├── Admin/         # Contrôleurs admin (protégés)
│   │   └── Auth/          # Authentification
│   └── Middleware/        # AdminMiddleware
├── Models/                # Eloquent (Service, Commande, Contact, Facture...)
└── Services/              # PaiementService, FactureService

database/
├── migrations/            # Tables SQL
└── seeders/               # Données initiales (services, admin, paramètres)

resources/
├── js/
│   ├── app.jsx            # Point d'entrée React
│   ├── context/           # Auth + Toast context
│   ├── pages/
│   │   ├── public/        # HomePage, CommandePage, PaiementSucces...
│   │   └── admin/         # Dashboard, Commandes, Services...
│   ├── components/
│   │   ├── public/        # Navbar, Footer
│   │   ├── admin/         # AdminLayout
│   │   └── shared/        # PrivateRoute
│   └── utils/
│       └── api.js         # Axios configuré + helpers prix
├── css/
│   └── app.css            # Tailwind
└── views/
    ├── app.blade.php      # SPA shell
    └── pdf/
        └── facture.blade.php  # Template facture PDF

routes/
├── web.php                # SPA fallback
└── api.php                # Toutes les routes API
```

---

## 🔒 Sécurité

- Authentification par token Sanctum
- Middleware `AdminMiddleware` sur toutes les routes admin
- Validation des données côté serveur (FormRequests)
- Protection CSRF
- Variables sensibles uniquement dans `.env`
- Pas de credentials dans le code source

---

## 📧 Emails

Configurer SMTP dans `.env` :
- Gmail : utiliser un App Password (pas le mot de passe du compte)
- Mailgun, Postmark, ou SMTP hébergeur recommandé en production

---

## 🗄 Base de données

Tables créées par migration :
- `users` — Administrateurs
- `services` — Services proposés (web, excel, survey, formation)
- `commandes` — Commandes clients + données paiement
- `contacts` — Messages formulaire contact
- `blog_articles` — Articles du blog
- `factures` — Factures PDF générées
- `system_logs` — Logs système
- `parametres` — Configuration dynamique (TVA, clés paiement...)
- `personal_access_tokens` — Tokens Sanctum

---

## 📞 Support

**Shalom Digital Solutions**
- 📍 Abomey-Calavi, Bénin
- 📞 +229 01 69 35 17 66
- ✉️ liferopro@gmail.com
- 💬 WhatsApp : +229 01 94 59 25 67
