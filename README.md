# 🎲 Jeux Partage

**Jeux Partage** est une application Symfony qui met en relation des amateurs et passionnés de jeux de société pour trouver des partenaires et organiser des rencontres conviviales.

## Fonctionnalités principales

- Création et gestion de comptes utilisateurs
- Gestion d'événements et de rencontres
- Inscription à des séances de jeux organsisés par les autres utilisateurs
- Géolocalisation via l'API de géocodage du gouvernement français (avec autocomplétion de l'adresse)
- Cartographie interactive avec **Leaflet**
- Sélecteurs améliorés avec **Tom Select**
- Système de messagerie en temps réel avec **Mercure**
- Administration complète (utilisateurs, jeux, catégories, événements, messages)

---

## Prérequis

- PHP >= 8.2
- Symfony 7.3
- Composer
- MySQL (ou MariaDB compatible)
- Node.js et npm (pour Webpack Encore)
- Docker et Docker Compose (optionnel, pour Mercure)

Vérifiez vos versions :

```bash
php --version
composer --version
node --version
npm --version
```

---

## Installation et démarrage (rapide)

1. Clonez le dépôt :

	```bash
	git clone https://github.com/CorentinPolard/jeux-partage.git
	cd jeux-partage
	```

2. Copiez les variables d'environnement et configurez `.env.local` :

	```bash
	cp .env.example .env.local
	```

	Éditez `.env.local` pour configurer vos paramètres :

	```env
	# Configuration de la base de données
	DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=8.0.32&charset=utf8mb4"
	
	# Clé secrète de l'application (générez-en une unique)
	APP_SECRET=VotreClefSecrete
	
	# Configuration Mercure (pour la messagerie temps réel)
	MERCURE_URL=http://mercure:3000/.well-known/mercure
	MERCURE_PUBLIC_URL=http://localhost:3000/.well-known/mercure
	MERCURE_JWT_SECRET="VotreSecretMercure"
	
	# Configuration du mailer
	MAILER_DSN=null://null
	```

3. Installez les dépendances PHP :

	```bash
	composer install
	```

4. Créez la base de données et exécutez les migrations :

	```bash
	# Avec la CLI Symfony
	symfony console doctrine:database:create
	symfony console doctrine:migrations:migrate
	
	# Ou avec PHP directement
	php bin/console doctrine:database:create
	php bin/console doctrine:migrations:migrate
	```

5. (Optionnel) Chargez les données de test :

	```bash
	symfony console doctrine:fixtures:load
	# ou
	php bin/console doctrine:fixtures:load
	```

6. Installez les dépendances JavaScript et compilez les assets :

	```bash
	npm install
	
	# Pour le développement (avec rechargement automatique)
	npm run watch
	
	# Ou pour la production
	npm run build
	```

7. (Optionnel) Lancez le hub Mercure avec Docker :

	```bash
	docker-compose up -d
	```

8. Lancez le serveur de développement :

	```bash
	# Avec la CLI Symfony (recommandé)
	symfony serve
	
	# Ou avec le serveur PHP intégré
	php -S 127.0.0.1:8000 -t public
	```

9. Accédez à l'application :

	Visitez l'URL retournée par `symfony serve` (généralement `http://localhost:8000`).

---

## Tests

Le projet contient des configurations pour PHPUnit. Pour lancer les tests :

```bash
# si phpunit est installé via composer
./vendor/bin/phpunit
# ou
php bin/phpunit
```

---

## Scripts npm disponibles

- `npm run dev` — Compilation en mode développement
- `npm run watch` — Compilation en mode développement avec rechargement automatique
- `npm run build` — Compilation optimisée pour la production
- `npm run dev-server` — Serveur de développement Webpack avec hot reload

Les fichiers compilés sont placés dans `public/build/`.

---

## Technologies utilisées

### Backend
- **Symfony 7.3** - Framework PHP
- **Doctrine ORM** - Gestion de la base de données
- **Symfony Security** - Authentification et autorisation
- **Mercure** - Communication temps réel (SSE)

### Frontend
- **JavaScript Vanilla** - Scripts personnalisés modulaires
- **Leaflet** - Cartographie interactive
- **Tom Select** - Sélecteurs avancés
- **Webpack Encore** - Compilation des assets

### Base de données
- **MySQL 8.0** - Stockage des données

---

## Structure du projet

- `src/Controller/` - Contrôleurs de l'application
- `src/Entity/` - Entités Doctrine
- `src/Form/` - Formulaires Symfony
- `src/Repository/` - Repositories Doctrine
- `src/Security/` - Configuration de sécurité
- `src/Service/` - Services métier
- `templates/` - Templates Twig
- `assets/` - Fichiers JavaScript et CSS
- `migrations/` - Migrations de base de données
- `tests/` - Tests unitaires et fonctionnels

---

## Docker

Le projet inclut des fichiers Docker Compose pour faciliter le déploiement :

- `compose.yaml` - Configuration de base
- `compose.override.yaml` - Configuration pour le développement
- `compose.prod.yaml` - Configuration pour la production

Pour lancer l'application avec Docker :

```bash
docker-compose up -d
```

---

## Auteur
© 2025 Corentin Polard — Tous droits réservés.

Le code source est fourni à titre de consultation.