# 🎲 Jeux Partage

**Jeux Partage** est une application Symfony qui a pour objectif de mettre en relation des amateurs et passionnés de jeux de société afin de rencontrer des partenaires de jeux et passer des moments convivaux autour d'un centre d'intérêt commun.  

## Fonctionnalités principales

- Création et gestion de compte utilisateur  
- Recherche de partenaires de jeu selon leurs centres d’intérêt  
- Géolocalisation grâce à l’API de géocodage du gouvernement  
- Intégration de **Leaflet** pour la cartographie interactive  
- Sélecteurs améliorés avec **Tom Select**  

---

## Prérequis

- **PHP** : 8.3.14  
- **Symfony** : 7.3.3  
- **Composer**  
- **MySQL** (ou MariaDB compatible)  
- **Node.js** + **npm** (pour Webpack Encore)  

---

## Installation

- Clonez le dépôt
- Créez un fichier .env à la racine du projet et configurez vos variables en suivant le .env.example.

- Exécutez les commandes suivantes pour créer et initialiser la base :
```php
symfony console doctrine:database:create
symfony console doctrine:migrations:migrate
```
- installez les dépendances PHP et JavaScript :  
```bash
# Dépendances PHP
composer install

# Dépendances JS
npm install
```

Pour lancer l'application : 
```bash
npm run build ou npm run watch
symfony serve
```
---
## Auteur
Corentin Polard