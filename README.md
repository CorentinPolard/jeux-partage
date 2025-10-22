# 🎲 Jeux Partage

**Jeux Partage** est une application Symfony qui met en relation des amateurs et passionnés de jeux de société pour trouver des partenaires et organiser des rencontres conviviales.

## Fonctionnalités principales

- Création et gestion de comptes utilisateurs
- Recherche de partenaires selon centres d'intérêt
- Géolocalisation via l'API de géocodage du gouvernement
- Cartographie interactive avec **Leaflet**
- Sélecteurs améliorés avec **Tom Select**

---

## Prérequis

- PHP >= 8.3 (ex. 8.3.14)
- Symfony 7.x (ex. 7.3)
- Composer
- MySQL (ou MariaDB compatible)
- Node.js et npm (pour Webpack Encore)

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

	git clone <repo-url>
	cd jeux-partage

2. Copiez les variables d'environnement et éditez `.env` :

	cp .env.example .env
	# puis éditez .env.local pour configurer DATABASE_URL et autres variables

	Exemple de `DATABASE_URL` :

	```env
	DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=8.0.32&charset=utf8mb4"
	```

3. Installer les dépendances PHP :

	```bash
	composer install
	```

4. Créer la base de données et exécuter les migrations :

	(Si vous avez la CLI Symfony)
	```bash
	symfony console doctrine:database:create
	symfony console doctrine:migrations:migrate
	```

	(Sinon utilisez php directement)
	```bash
	php bin/console doctrine:database:create
	php bin/console doctrine:migrations:migrate
	```

5. Installer les dépendances JavaScript et compiler les assets :

	```bash
	npm install
	# Pour développement
	npm run watch
	# Ou build pour production
	npm run build
	```

6. Lancer le serveur de développement :

	```bash
	# Avec la CLI Symfony
	symfony serve

	# Ou alternative PHP built-in pour tests rapides
	php -S 127.0.0.1:8000 -t public
	```

Visitez ensuite l'URL retournée par `symfony serve`.

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

## Scripts utiles (npm)

Consultez `package.json` pour la liste complète des scripts. Exemples courants :

- `npm run dev` ou `npm run watch` — compilation en mode développement (avec watch)
- `npm run build` — compilation pour production

Webpack Encore place les fichiers compilés dans `public/build/`.

---

## Lint / Qualité (optionnel)

Si vous utilisez PHPStan, ESLint, ou des workflows CI, ajoutez ici les commandes pour lancer les vérifications locales.

---

## Auteur
© 2025 Corentin Polard — Tous droits réservés.

Le code source est fourni à titre de consultation.