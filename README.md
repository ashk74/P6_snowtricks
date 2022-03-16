# Project 6 : SnowTricks (Application developer - PHP / Symfony - OpenClassrooms)

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/34c533240abb42e9b4615e448f03c139)](https://www.codacy.com/gh/ashk74/P6_snowtricks/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=ashk74/P6_snowtricks&amp;utm_campaign=Badge_Grade)

## TECHNICAL REQUIREMENTS
### Web Server
*   PHP 7.2.5 or higher
*   PHP extensions : Ctype, iconv, JSON, PCRE, Session, SimpleXML and Tokenizer
*   SQL DBMS

*   Versions used in this project
    *   Apache 2.4.46
    *   MySQL 5.7.34
    *   PHP 8.1.3

### SMTP Server
*   Used for email verification and reset password

### Composer
*   [How to install Composer ?](https://getcomposer.org/download/)

### CSS Framework + Icons toolkit (base.html.twig)
*   Bootstrap 5.1.3
*   Font-awesome 6
*   Bootstrap-icon 1.8.1

## Installation
### 1.  Download or clone the project
*   Download zip files or clone the project repository with github - [GitHub documentation](https://docs.github.com/en/github/creating-cloning-and-archiving-repositories/cloning-a-repository)

### 2.  Edit .env file
```yaml
# SQL DBMS
DATABASE_URL="mysql://username:password@host:port/dbname"

# Mailer
MAILER_DSN=gmail://username:"password"@default?verify_peer=0
MAILER_EMAIL=your@email.com

# SnowTricks account
ACCOUNT_FULLNAME="Firstname Lastname"
ACCOUNT_EMAIL=your@email.com
ACCOUNT_PASSWORD=your_password
```

### 3.  Set your PHP version
*   List and select PHP version (minimum 7.2.5)
```bash
symfony local:php:list
```
*   Set your PHP version
```
echo 8.1.3 > .php-version
```

### 4.  Install packages needed
#### Run your terminal at root project
```bash
composer install
```

### 5.  Create database and tables
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### 6.  Load fixtures with pictures and avatar
```bash
php bin/console doctrine:fixtures:load
```
### 7. Connexion
*   Use your account informations set inside the .env file to login

### Great ! You can now discover SnowTricks and start publishing :)
