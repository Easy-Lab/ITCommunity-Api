##Travis CI

[![Build Status](https://travis-ci.org/Easy-Lab/ITCommunity-Api.svg?branch=develop)](https://travis-ci.org/Easy-Lab/ITCommunity-Api)

## Description

ITCommunity is a project of two students from IPSSI. Their goal? To create a community platform between IT enthusiasts and put them in touch with people in need of advice!

## Installation

**Clone repository**

`git clone git@github.com:Easy-Lab/ITCommunity-Api.git`

**Launch Docker container**

`docker-compose up -d`

**Access to the container**

`docker-compose exec web /bin/bash`

**Install dependencies with composer**

`composer install`

**Generating the Public and Private Key JWT**

Default generate path config/jwt/
```
$ openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
$ openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout
```

**Copy .env.dist to .env**

`cp .env.dist .env`

**Edit .env**

Modify all environment variables according to your configuration. [View all variables.](https://github.com/Easy-Lab/ITCommunity-Api/blob/develop/ENV.md)

- Modifier le .env :
```bash
DATABASE_URL=mysql://root:root@database:3306/symfonyapi
```

**Setup database**

`php bin/console d:s:u --force`

**Generating fixtures**

`php bin/console hautelook:fixtures:load`

## Access

**To access Nelmio's doc in the browser**
  
`localhost`

**To access phpMyAdmin**

`localhost:8080`
- User : root
- Password :  root

## Test

```bash
php bin/phpunit
```

## Example of use

[Cliquer ici](https://github.com/Easy-Lab/ITCommunity-Api/blob/develop/EXAMPLES.md)
