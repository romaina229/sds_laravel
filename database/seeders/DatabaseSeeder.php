<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Service;
use App\Models\Parametre;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::firstOrCreate(
            ['email' => 'adminsds@gmail.com'],
            [
                'name'              => 'Administrateur',
                'password'          => Hash::make('Shalom@2026!'),
                'role'              => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Services
        $services = [
            ['categorie'=>'web','nom'=>'Site Vitrine Standard','description'=>'Présentez votre activité avec un site web élégant et professionnel. Parfait pour les indépendants et petites entreprises.','prix_fcfa'=>150000,'prix_euro'=>230,'duree'=>'2-3 semaines','icone'=>'fas fa-laptop-code','popular'=>false,'actif'=>true,'features'=>json_encode(['Design responsive moderne','5 pages maximum','Formulaire de contact','Optimisation SEO de base','Hébergement inclus 1 an','Support technique 3 mois'])],
            ['categorie'=>'web','nom'=>'Boutique E-commerce','description'=>'Vendez vos produits en ligne avec une boutique complète et sécurisée.','prix_fcfa'=>450000,'prix_euro'=>685,'duree'=>'4-6 semaines','icone'=>'fas fa-shopping-cart','popular'=>true,'actif'=>true,'features'=>json_encode(['Catalogue produits illimité','Paiement en ligne sécurisé','Gestion des stocks','Interface admin complète','Hébergement 1 an inclus','Support technique 6 mois'])],
            ['categorie'=>'web','nom'=>'Site sur Mesure','description'=>'Solution personnalisée développée selon vos besoins spécifiques.','prix_fcfa'=>850000,'prix_euro'=>1295,'duree'=>'8+ semaines','icone'=>'fas fa-rocket','popular'=>false,'actif'=>true,'features'=>json_encode(['Analyse approfondie de vos besoins','Développement spécifique','Intégration API externes','Base de données personnalisée','Formation complète','Support technique 12 mois'])],
            ['categorie'=>'excel','nom'=>'Excel Avancé','description'=>'Création de tableaux de bord automatisés, macros VBA et solutions de gestion de données complexes.','prix_fcfa'=>75000,'prix_euro'=>115,'duree'=>'1-2 semaines','icone'=>'fas fa-file-excel','popular'=>false,'actif'=>true,'features'=>json_encode(['Tableaux croisés dynamiques avancés','Macros et automatisation VBA','Tableaux de bord interactifs','Formules complexes et matrices','Mise en forme conditionnelle','Intégration avec autres applications'])],
            ['categorie'=>'excel','nom'=>'Automatisation Office','description'=>'Automatisation des tâches répétitives avec la suite Microsoft Office.','prix_fcfa'=>50000,'prix_euro'=>76,'duree'=>'1 semaine','icone'=>'fas fa-file-word','popular'=>false,'actif'=>true,'features'=>json_encode(['Modèles Word automatisés','Publipostage avancé','Présentations PowerPoint interactives','Automatisation Power Automate','Intégration entre applications Office','Scripts et raccourcis'])],
            ['categorie'=>'excel','nom'=>'Analyse de Données','description'=>'Analyse approfondie et visualisation de vos données métier.','prix_fcfa'=>100000,'prix_euro'=>152,'duree'=>'2-3 semaines','icone'=>'fas fa-chart-line','popular'=>false,'actif'=>true,'features'=>json_encode(['Nettoyage et préparation des données','Analyse statistique avancée','Rapports automatisés','Visualisations Power BI','Prédictions et modélisation','Tableaux de bord exécutifs'])],
            ['categorie'=>'survey','nom'=>'KoboToolbox / ODK','description'=>'Mise en place de systèmes de collecte de données mobiles pour la recherche et le monitoring.','prix_fcfa'=>120000,'prix_euro'=>183,'duree'=>'2-3 semaines','icone'=>'fas fa-clipboard-list','popular'=>false,'actif'=>true,'features'=>json_encode(['Conception de formulaires complexes','Déploiement sur appareils mobiles','Synchronisation des données','Export et analyse des données','Configuration des validations','Formation à l\'utilisation'])],
            ['categorie'=>'survey','nom'=>'SurveyCTO / Survey Solution','description'=>'Solutions professionnelles de collecte de données pour la recherche académique.','prix_fcfa'=>150000,'prix_euro'=>228,'duree'=>'3-4 semaines','icone'=>'fas fa-poll','popular'=>false,'actif'=>true,'features'=>json_encode(['Configuration serveur SurveyCTO','Formulaires complexes avec logiques','Gestion des équipes et superviseurs','Monitoring en temps réel','Intégration Stata/R/Python','Support technique complet'])],
            ['categorie'=>'survey','nom'=>'Google Forms & Surveys','description'=>'Création de formulaires et enquêtes en ligne avancés avec intégration Google Workspace.','prix_fcfa'=>40000,'prix_euro'=>61,'duree'=>'3-5 jours','icone'=>'fab fa-google','popular'=>false,'actif'=>true,'features'=>json_encode(['Formulaires Google avancés','Intégration Google Sheets','Logiques conditionnelles complexes','Design personnalisé','Automatisation des réponses','Tableaux de bord de résultats'])],
            ['categorie'=>'formation','nom'=>'Formation Excel Avancé','description'=>'Formation intensive aux fonctionnalités avancées d\'Excel.','prix_fcfa'=>50000,'prix_euro'=>76,'duree'=>'2 jours','icone'=>'fas fa-graduation-cap','popular'=>false,'actif'=>true,'features'=>json_encode(['Formation présentiel ou en ligne','Supports de cours complets','Exercices pratiques','Cas réels entreprise','Certification de fin de formation','Support post-formation 1 mois'])],
            ['categorie'=>'formation','nom'=>'Formation Collecte de Données','description'=>'Maîtrise des outils modernes de collecte de données mobiles.','prix_fcfa'=>80000,'prix_euro'=>122,'duree'=>'3 jours','icone'=>'fas fa-mobile-alt','popular'=>false,'actif'=>true,'features'=>json_encode(['Formation KoboToolbox/ODK','Création de formulaires complexes','Gestion des données collectées','Déploiement sur le terrain','Bonnes pratiques de collecte','Support technique inclus'])],
            ['categorie'=>'formation','nom'=>'Accompagnement Personnalisé','description'=>'Coaching individuel ou en petit groupe. Programme adapté à vos objectifs.','prix_fcfa'=>100000,'prix_euro'=>152,'duree'=>'Flexible','icone'=>'fas fa-user-tie','popular'=>false,'actif'=>true,'features'=>json_encode(['Analyse de vos besoins spécifiques','Programme de formation sur mesure','Accompagnement pas à pas','Support email et téléphone','Révision de vos projets','Certificat de compétence'])],
            ['categorie'=>'formation','nom'=>'Accompagnement réalisation des enquêtes','description'=>'Accompagnement des organisations dans la réalisation de leur mission d\'enquête.','prix_fcfa'=>50000,'prix_euro'=>35,'duree'=>'3 jours','icone'=>'fas fa-chart-line','popular'=>true,'actif'=>true,'features'=>json_encode(['Analyse approfondie de vos besoins','Réalisation pro Max','Accompagnement tout au long de la mission'])],

            // Matériels & Maintenance
            ['categorie'=>'materiel','nom'=>'Fourniture de matériels informatiques','description'=>'Acquisition et livraison d\'ordinateurs, imprimantes et périphériques neufs ou reconditionnés pour entreprises, ONG et particuliers.','prix_fcfa'=>0,'prix_euro'=>0,'duree'=>'Sur devis','icone'=>'fas fa-desktop','popular'=>false,'actif'=>true,'features'=>json_encode(['Ordinateurs de bureau et portables','Imprimantes et scanners','Périphériques (écrans, claviers, souris)','Matériels neufs et reconditionnés','Livraison et installation incluses','Garantie fournisseur'])],
            ['categorie'=>'materiel','nom'=>'Didacticiels & logiciels','description'=>'Vente, installation et configuration de logiciels éducatifs, bureautiques et professionnels adaptés à vos besoins.','prix_fcfa'=>0,'prix_euro'=>0,'duree'=>'Sur devis','icone'=>'fas fa-compact-disc','popular'=>false,'actif'=>true,'features'=>json_encode(['Logiciels bureautiques (Microsoft Office, LibreOffice)','Logiciels éducatifs et de formation','Antivirus et sécurité','Installation et configuration','Formation à l\'utilisation','Licences officielles'])],
            ['categorie'=>'materiel','nom'=>'Consommables & accessoires','description'=>'Fourniture de cartouches d\'encre, câbles, clés USB, disques durs et tout consommable informatique pour votre quotidien.','prix_fcfa'=>0,'prix_euro'=>0,'duree'=>'Sur devis','icone'=>'fas fa-plug','popular'=>false,'actif'=>true,'features'=>json_encode(['Cartouches d\'encre et toners','Câbles et connectiques','Clés USB et disques durs externes','Accessoires de bureau','Livraison rapide','Prix compétitifs'])],
            ['categorie'=>'materiel','nom'=>'Maintenance informatique','description'=>'Diagnostic, réparation, mise à jour et entretien préventif de vos équipements informatiques pour garantir leur bon fonctionnement.','prix_fcfa'=>0,'prix_euro'=>0,'duree'=>'Sur devis','icone'=>'fas fa-tools','popular'=>true,'actif'=>true,'features'=>json_encode(['Diagnostic matériel et logiciel','Réparation et remplacement de pièces','Mise à jour des systèmes','Nettoyage et optimisation','Maintenance préventive','Intervention sur site'])],
        ];
        
        foreach ($services as $s) {
            Service::firstOrCreate(['nom' => $s['nom']], $s);
        }

        // Paramètres
        $parametres = [
            ['cle'=>'taux_aib','valeur'=>'0.05','groupe'=>'fiscal'],
            ['cle'=>'site_nom','valeur'=>'Shalom Digital Solutions','groupe'=>'general'],
            ['cle'=>'site_email','valeur'=>'liferopro@gmail.com','groupe'=>'general'],
            ['cle'=>'site_telephone','valeur'=>'+229 01 69 35 17 66','groupe'=>'general'],
            ['cle'=>'site_whatsapp','valeur'=>'+22994592567','groupe'=>'general'],
            ['cle'=>'site_adresse','valeur'=>'Abomey-Calavi, Bénin','groupe'=>'general'],
            ['cle'=>'maintenance_mode','valeur'=>'0','groupe'=>'system'],
            ['cle'=>'fedapay_public_key','valeur'=>'','groupe'=>'paiement'],
            ['cle'=>'fedapay_secret_key','valeur'=>'','groupe'=>'paiement'],
            ['cle'=>'fedapay_environment','valeur'=>'sandbox','groupe'=>'paiement'],
            ['cle'=>'cinetpay_api_key','valeur'=>'','groupe'=>'paiement'],
            ['cle'=>'cinetpay_site_id','valeur'=>'','groupe'=>'paiement'],
        ];

        foreach ($parametres as $p) {
            Parametre::firstOrCreate(['cle' => $p['cle']], $p);
        }
    }
}