# Documentation NewsManager

NewsManager est un package Laravel modulable dédié à la gestion des actualités dans votre application. Conçu pour s'intégrer de manière transparente dans l'écosystème Laravel, il regroupe plusieurs fonctionnalités essentielles pour administrer facilement des contenus sous forme de news, de médias (images, vidéos, etc.) et de documents.

Le package offre également une intégration avec Laravel Breeze, avec des commandes d'installation dédiées pour chaque stack (Blade, React, Vue) et la possibilité d'ajouter des modules optionnels via une commande séparée.

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
  - **Actualités :** Créez, modifiez, affichez et supprimez des articles/news.
  - **Médias :** Téléversez et gérez images, vidéos et autres supports.
  - **Documents :** Importez et organisez des documents (PDF, Word, etc.) associés aux actualités ou indépendants.

- **Intégration avec Laravel Breeze :**
  - **Installation automatisée :** Des commandes d'installation dédiées permettent de configurer Laravel Breeze sur la stack choisie (Blade, React ou Vue).
  - **Installation personnalisée des modules :** Une commande spécifique (`news:modules`) permet d'ajouter les modules optionnels (news, media, documents).

- **Modularité et Extensibilité :**
  - Ressources (vues, routes, contrôleurs, migrations) importées dynamiquement en fonction de la stack et des modules activés.
  - Commandes artisan dédiées pour faciliter l'installation et l'ajout des modules.

---

## Installation

### Via Composer

Vous pouvez installer le package en **production** ou en **développement** sans utiliser la notation `:dev-master`.

- **En Production (version stable) :**

  ```bash
  composer require aristechdev/news-manager:1.0.0
  ```

- **En Développement (version en cours, branche de développement) :**

  ```bash
  composer require aristechdev/news-manager:dev-develop
  ```

> **Remarque :**  
> Nous ne recommandons pas l'utilisation de `:dev-master`. Utilisez la version stable `1.0.0` pour la production et la branche `dev-develop` pour le développement.

---

## Installation Interactive

### A. Installation de la Stack

Selon la technologie frontale souhaitée, le package propose des commandes d'installation dédiées :

- Pour la stack **Blade** :

  ```bash
  php artisan aristechnews:install:breeze

- Pour la stack **Blade** :

  ```bash
  php artisan aristechnews:install:blade
  ```

- Pour la stack **React** :

  ```bash
  php artisan aristechnews:install:react
  ```

- Pour la stack **Vue** :

  ```bash
  php artisan aristechnews:install:vue
  ```

Ces commandes vérifieront la présence de Laravel Breeze, créeront automatiquement le fichier `welcome.blade.php` adapté et lanceront la commande `breeze:install` avec la stack correspondante.

### B. Installation des Modules

Ensuite, pour ajouter les modules complémentaires (news, media et documents), utilisez la commande :

```bash
php artisan aristechnews:modules
```

Vous pouvez également passer l'option `--modules` pour une installation non-interactive (exemple, installer uniquement "news" et "media") :

```bash
php artisan aristechnews:modules --modules=news,media
```

---

## Configuration

Après l'installation, adaptez le fichier de configuration `config/newsmanager.php` afin de définir la stack active et la liste des modules à charger :

```php
return [
    'stack'   => env('NEWSMANAGER_STACK', 'blade'), // blade, react ou vue
    'modules' => env('NEWSMANAGER_MODULES', 'news,media,documents') !== '' 
                    ? array_map('trim', explode(',', env('NEWSMANAGER_MODULES', 'news,media,documents')))
                    : [],
    // Autres options de configuration...
];
```

Définissez également les variables d'environnement dans votre fichier `.env` :

```dotenv
NEWSMANAGER_STACK=blade
NEWSMANAGER_MODULES=news,media,documents
```

---

## Structure et Organisation

Le package est structuré de manière modulaire pour permettre une grande flexibilité :

```
packages/AristechDev/NewsManager/
├── config/
│   ├── news.php
│   └── newsmanager.php
├── resources/
│   ├── Blade/
│   │   └── views/
│   │       ├── index.blade.php
│   │       └── welcome.blade.php
│   ├── React/
│   │   └── views/
│   ├── VueJs/
│   │   └── views/
│   └── views/   (vues génériques)
├── routes/
│   ├── web.php         (routes pour Blade)
│   ├── react.php       (routes pour React)
│   └── vue.php         (routes pour Vue)
└── src/
    ├── Console/
    │   ├── Commands/
    │   │   ├── InstallNewsPackageBlade.php
    │   │   ├── InstallNewsPackageReact.php
    │   │   ├── InstallNewsPackageVue.php
    │   │   └── InstallNewsModules.php
    ├── Database/
    │   └── migrations/
    ├── Http/
    │   └── Controllers/
    │       ├── Blade/
    │       ├── React/
    │       └── VueJs/
    └── Providers/
        └── NewsManagerServiceProvider.php
```

Les vues, routes et contrôleurs sont importés dynamiquement en fonction de la stack active et des modules activés dans la configuration.

---

## Utilisation

Une fois le package installé et configuré :

1. Vérifiez et adaptez le fichier de configuration `config/newsmanager.php`.
2. Exécutez les migrations :

   ```bash
   php artisan migrate
   ```

3. Utilisez le package pour gérer vos contenus de manière modulaire selon vos besoins.

---

## Licence

Ce package est sous licence **MIT**.
