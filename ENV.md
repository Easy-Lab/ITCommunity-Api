## Description

In this document are described all the environment variables necessary to configure for the project.

## doctrine/doctrine-bundle

If you use docker on your local machine you can leave the default configuration

`DATABASE_URL=mysql://root:root@database:3306/symfonyapi`

In other cases you have to change the username, password and database name

## lexik/jwt-authentication-bundle

**Path to private key**

`JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem` 

**Path to public key**

`JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem`

**Your passphrase (during key generation a paspharasse should be entered).**

`JWT_PASSPHRASE=75cc06031b56099860d4213dadb18289`

**Token lifetime** 

`JWT_TOKENTTL=3600`

## symfony/swiftmailer-bundle

**Mailjet id**

USER_MAILJET=your_id

**Mailjet password**

PASSWORD_MAILJET=yout_password

**Mail to receive the email from contact form and bug report**

MAILER_FROM_CONTACT=your_email

**Email for automatic email**

MAILER_FROM_ADDRESS=automatic_email

**Email label**

MAILER_FROM_LABEL=your_label

## goole api key

**Use to geolocate user**

GOOGLE_GEOCODING_API_KEY=your_geocoding_api_key

## url front

**Url front**

URL_FRONT=url

## nelmio/cors-bundle

**If you use docker on your local machine you can leave the default configuration**

CORS_ALLOW_ORIGIN=^https?://(localhost|127\.0\.0\.1)(:[0-9]+)?$

**In other cases you have to change value**
