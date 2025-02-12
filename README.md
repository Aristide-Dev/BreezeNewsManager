# Documentation NewsManager

NewsManager est un package Laravel modulable dédié à la gestion des actualités dans votre application. Conçu pour s'intégrer de manière transparente dans l'écosystème Laravel, il regroupe plusieurs fonctionnalités essentielles pour administrer facilement des contenus sous forme de news, de médias (images, vidéos, etc.) et de documents. Il permet également d’installer rapidement le système d’authentification de Laravel Breeze et de choisir, dès l’installation, la technologie frontale adaptée à votre projet.

---

## Profil de l'auteur

**Nom :** Gnimassou  
**Prénom :** Jean-Marie Aristide  
**Email :** [aristechdev@gmail.com](mailto:aristechdev@gmail.com)

### Signature DREAMER

```
Signature DREAMER:
 _______   _______   ________   ______   __       __  ________  _______  
/       \ /       \ /        | /      \ /  \     /  |/        |/       \ 
$$$$$$$  |$$$$$$$  |$$$$$$$$/ /$$$$$$  |$$  \   /$$ |$$$$$$$$/ $$$$$$$  |
$$ |  $$ |$$ |__$$ |$$ |__    $$ |__$$ |$$$  \ /$$$ |$$ |__    $$ |__$$ |
$$ |  $$ |$$    $$< $$    |   $$    $$ |$$$$  /$$$$ |$$    |   $$    $$< 
$$ |  $$ |$$$$$$$  |$$$$$/    $$$$$$$$ |$$ $$ $$/$$ |$$$$$/    $$$$$$$  |
$$ |__$$ |$$ |  $$ |$$ |_____ $$ |  $$ |$$ |$$$/ $$ |$$ |_____ $$ |  $$ |
$$    $$/ $$ |  $$ |$$       |$$ |  $$ |$$ | $/  $$ |$$       |$$ |  $$ |
$$$$$$$/  $$/   $$/ $$$$$$$$/ $$/   $$/ $$/      $$/ $$$$$$$$/ $$/   $$/ 
```

---

## Fonctionnalités Clés

- **Gestion complète des contenus :**
  - **Actualités :** Créez, modifiez, affichez et supprimez des articles ou news.
  - **Médias :** Téléversement, gestion et affichage d’images, vidéos ou autres supports.
  - **Documents :** Importation et organisation de documents (PDF, Word, etc.) avec la possibilité de les associer à des actualités ou de les gérer de manière indépendante.

- **Intégration avec Laravel Breeze :**
  - **Installation automatisée de Breeze :** Le package intègre une commande artisan personnalisée qui interroge l’utilisateur (ou lit une configuration par défaut) pour choisir la stack frontale à utiliser (Blade, React ou Vue).
  - **Scaffolding d’authentification :** Selon le choix, la commande `breeze:install` est lancée afin d’installer rapidement les routes, contrôleurs et vues liés à l’authentification (connexion, inscription, réinitialisation de mot de passe, etc.).
  - **Flexibilité :** Permet de personnaliser et d’étendre le système d’authentification via des templates Blade ou une solution SPA avec Inertia.

---

## Installation

### Via Composer

Utilisez la commande suivante pour installer le package :

```bash
composer require aristechdev/news-manager:1.0.0
```

### Installation avec dépôt local

1. **Ajoutez le repository**  
   Dans le fichier `composer.json` de la racine de votre application Laravel, ajoutez :

   ```json
   {
       "repositories": [
           {
               "type": "path",
               "url": "./packages/AristechDev/NewsManager"
           }
       ]
   }
   ```

2. **Installez le package :**

   ```bash
   composer require aristechdev/news-manager:dev-master
   ```

3. **Publiez la configuration (si besoin) :**

   ```bash
   php artisan vendor:publish --tag=newsmanager-config
   ```

4. **Lancez l'installation interactive :**

   ```bash
   php artisan news:install
   ```

---

## Configuration Interactive

Lancez la commande d'installation interactive avec :

```bash
php artisan news:install
```

Cette commande guide l'utilisateur via des prompts pour définir le nom du package, choisir la stack frontale (Blade, React ou Vue) et réaliser les premières configurations.  
Pour les environnements automatisés ou CI/CD, utilisez l'option suivante pour désactiver l'interaction manuelle :

```bash
php artisan news:install --stack=blade --no-interaction
```

---

## Structure Modulaire et Extensible

Le package est organisé en modules distincts (contrôleurs, modèles, routes, vues, migrations, etc.) pour faciliter la maintenance et l’extension de ses fonctionnalités.  
Grâce à cette architecture, il est facile d'ajouter ou de modifier des modules spécifiques en fonction de l'évolution des besoins de votre application.

---

## Automatisation du Processus d'Installation

En lançant la commande `php artisan news:install`, l'ensemble du processus d'installation est automatisé :
- Choix de la stack frontale.
- Installation de Laravel Breeze.
- Exécution des migrations.
- Instructions pour la compilation des assets (via `npm`).

---

## Licence

Ce package est sous licence **MIT**.

---

## Utilisation

Par défaut, le package charge ses routes sous le préfixe `newsmanager`.  
Pour accéder à la gestion des actualités, rendez-vous sur l'URL correspondante dans votre application.

---

Cette version en Markdown offre une présentation claire et structurée, facilitant la lecture et la maintenance du document. N’hésitez pas à ajuster ou personnaliser selon vos besoins spécifiques.
