# NewsManager

NewsManager est un package Laravel modulable dédié à la gestion des actualités dans votre application. Il regroupe plusieurs fonctionnalités essentielles pour administrer facilement des contenus sous forme de news, de médias (images, vidéos, etc.) et de documents. Conçu pour s'intégrer de manière transparente dans l'écosystème Laravel, il vous permet d'installer rapidement le système d'authentification de Laravel Breeze et de choisir, dès l'installation, la technologie frontale qui convient à votre projet.

## Fonctionnalités Clés

- **Gestion complète des contenus :**
  - **Actualités :** Créez, modifiez, affichez et supprimez des articles ou news.
  - **Médias :** Téléversement, gestion et affichage d'images, vidéos ou autres supports.
  - **Documents :** Importation et organisation de documents (PDF, Word, etc.), avec possibilité de les associer à des actualités ou de les gérer de manière indépendante.

- **Intégration avec Laravel Breeze :**
  - **Installation automatisée de Breeze :** Le package intègre une commande artisan personnalisée qui, dès l'exécution, interroge l'utilisateur (ou lit une configuration par défaut) pour choisir la stack frontale à utiliser (Blade, React ou Vue).
  - **Scaffolding d'authentification :** Selon le choix, le package lance la commande `breeze:install` appropriée afin de mettre en place rapidement toutes les routes, contrôleurs et vues liés à l'authentification (connexion, inscription, réinitialisation de mot de passe, etc.).
  - **Flexibilité :** Permet à l'utilisateur final de personnaliser et d'étendre le système d'authentification, que ce soit en utilisant des templates Blade ou en optant pour une solution SPA via Inertia.

## Configuration Interactive

- **Commande d'installation personnalisée :**
  
  Utilisez la commande :
  
  ```bash
  php artisan news:install
  ```
  
  Cette commande guide l'utilisateur dès la première exécution via des prompts interactifs pour définir le nom du package, choisir la stack frontale et réaliser les premières configurations.

- **Publication de la configuration :**

  Le package offre la possibilité de publier un fichier de configuration personnalisable dans le dossier `config` de l'application hôte, permettant d'ajuster ultérieurement des options telles que le choix de la stack (définie via la variable d'environnement `NEWS_FRONTEND_STACK`).

## Structure Modulaire et Extensible

- **Organisation claire :**  
  Le package est structuré en modules distincts (contrôleurs, modèles, routes, vues, migrations, etc.) pour faciliter la maintenance et l'extension de ses fonctionnalités.

- **Possibilité d'ajouter des modules :**  
  Grâce à une architecture en dossiers (par exemple, un dossier dédié aux routes pour news, médias et documents), il est aisé d'ajouter ou de modifier des fonctionnalités spécifiques en fonction de l'évolution des besoins de votre application.

## Automatisation du Processus d'Installation

- **Exécution en une seule commande :**  
  L'utilisateur n'a qu'à lancer `php artisan news:install` pour que le package effectue l'ensemble des étapes : choix de la stack, installation de Breeze, exécution des migrations et affichage d'instructions pour la compilation des assets (via `npm`).

- **Mode non-interactif optionnel :**  
  Pour les environnements automatisés ou CI/CD, la commande peut être exécutée avec des options comme `--stack=blade --no-interaction`, afin d'éviter toute intervention manuelle.

## Installation

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

4. **Lancez la commande d'installation interactive :**

   ```bash
   php artisan news:install
   ```

## Licence

Ce package est sous licence MIT.

## Utilisation

Par défaut, le package charge ses routes sous le préfixe `newsmanager`.

Par exemple, accédez à : 