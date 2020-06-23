## Description

ITCommunity est un projet de deux étudiants de l'IPSSI. Leur but ? Créer une plateforme communautaire entre passionné d'informatique et les mettre en relation avec des personnes ayant besoin de conseils !

## Intallation

- Lancer docker : `docker-compose up -d`
- Accéder au container nginx : `docker-compose exec web /bin/bash`
- Installer les dépendances avec composer : `composer install`
- Modifier le .env :
```bash
DATABASE_URL=mysql://root:root@database:3306/symfonyapi
```
- Créée la bdd : `php bin/console doctrine:database:create`
- Update la bdd : `php bin/console d:s:u --force`
- Générer les fixtures : `php bin/console hautelook:fixtures:load`

## Test
```bash
php bin/phpunit
```
## Accès

##### Pour accéder à la doc de Nelmio : 

```bash
localhost
```

##### Pour acceder à phpMyAdmin :
```bash
localhost:8080
```
- Utilisateur : root
- Mot de passe :  root