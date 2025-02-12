# Documentation NewsManager

NewsManager est un package Laravel modulable dédié à la gestion des actualités dans votre application. Conçu pour s'intégrer de manière transparente dans l'écosystème Laravel, il regroupe plusieurs fonctionnalités essentielles pour administrer facilement des contenus sous forme de news, de médias (images, vidéos, etc.) et de documents.  
Le package offre également une intégration avec Laravel Breeze pour mettre en place un système d'authentification complet, avec la possibilité de choisir la stack frontale (Blade, React ou Vue). Enfin, l'installation des modules (news, media, documents) se fait via une commande dédiée.

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
  - **Médias :** Téléversement, gestion et affichage d'images, vidéos ou autres supports.
  - **Documents :** Importez et organisez vos documents (PDF, Word, etc.) avec la possibilité de les associer à des actualités ou de les gérer de manière indépendante.

- **Intégration avec Laravel Breeze :**
  - **Installation automatisée de Breeze :** Le package intègre une commande artisan personnalisée (`breeze:news`) qui interroge l'utilisateur (ou lit la configuration par défaut) pour choisir la stack frontale à utiliser (Blade, React ou Vue).
  - **Scaffolding d'authentification :** Selon le choix, la commande lance l'installation de Breeze via `breeze:install`, configurant ainsi les routes, contrôleurs et vues d'authentification.
  - **Installation conditionnée :** Si Laravel Breeze n'est pas présent, le package propose d'exécuter l'installation (ou affiche une instruction pour l'installation manuelle via Composer).

- **Installation et gestion des modules :**
  - **Commande dédiée :** Après la configuration initiale avec `breeze:news`, utilisez la commande `news:modules` pour choisir et installer les modules complémentaires (news, media, documents).
  - **Installation modulaire :** Sélectionnez via une interface interactive les modules souhaités ou installez-les l'un après l'autre selon vos besoins.

---

## Installation

### 1. Via Composer (version stable)

Installez la version stable du package via Composer :

```bash
composer require aristechdev/news-manager:1.0.1
```

### 2. Installation avec dépôt local

1. **Ajoutez le repository**  
   Dans le fichier `composer.json` à la racine de votre application Laravel, ajoutez :

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

2. **Installez le package** :

   ```bash
   composer require aristechdev/news-manager:dev-master
   ```

3. **Publiez la configuration (si besoin)** :

   ```bash
   php artisan vendor:publish --tag=newsmanager-config
   ```

---

## Configuration Interactive

### A. Installation de Laravel Breeze et configuration du package

Lancez la commande interactive suivante pour installer Laravel Breeze et configurer la stack frontale :

```bash
php artisan breeze:news
```

Cette commande effectue les tâches suivantes :

- Vérifie que Laravel Breeze est installé (et propose de l'installer sinon).
- Propose de choisir automatiquement ou manuellement la stack frontale à utiliser (Blade, React, Vue).
- Lance l'installation de Breeze avec la commande `breeze:install` et configure les éléments de base du package.

Pour un fonctionnement non interactif (utile en CI/CD) :

```bash
php artisan breeze:news --stack=blade --no-interaction
```

### B. Installation des modules complémentaires

Une fois la configuration initiale terminée, vous pouvez ajouter les modules supplémentaires (news, media, documents) via la commande :

```bash
php artisan news:modules
```

Cette commande vous permettra de sélectionner interactivement les modules à installer ou d'installer l'ensemble des modules.

---

## Structure Modulaire et Extensible

Le package est organisé en modules distincts :  
- **Contrôleurs, modèles, routes, vues, migrations** pour la gestion des actualités, médias et documents.  
- Une architecture en dossiers facilitant la maintenance et l'extension des fonctionnalités en fonction des besoins de votre application.

---

## Automatisation du Processus d'Installation

En lançant `php artisan breeze:news`, l'ensemble du processus d'installation est automatisé :
- Choix de la stack frontale et installation de Breeze.
- Publication de la configuration de base et des migrations.
- Instructions d'utilisation (exécution de "php artisan migrate" et compilation des assets via `npm`).

Par la suite, la commande `news:modules` permet d'ajouter ou de modifier les modules installés.

---

## Licence

Ce package est sous licence **MIT**.

---

## Utilisation

Par défaut, le package charge ses routes sous le préfixe `newsmanager`.  
Pour accéder à la gestion des actualités, rendez-vous sur l'URL correspondante dans votre application Laravel.

---

Cette documentation vous offre une présentation claire de l'installation et de la configuration du package NewsManager.  
N'hésitez pas à adapter ou personnaliser ces instructions selon les besoins spécifiques de votre projet.
