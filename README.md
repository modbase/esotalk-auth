# Esotalk External Authentication

A package that allows for external authentication with Esotalk, integrating
directly with the Laravel 4 framework.

Strongly based on the Fluxbb External Authenticator made by Franz Liedke.

## Installation

### Step 1: Install package through Composer

Add this line to the `require` section of your `composer.json`:

    "modbase/esotalk-auth": "1.0.*"

Alternately, you can use the Composer command-line tool by running this command:

    composer require modbase/esotalk-auth

Next, run `composer install` to actually install the package.

### Step 2: Register the service provider

In your Laravel application, edit the `app/config/app.php` file and add this
line to the `providers` array:

    'Modbase\EsotalkAuth\EsotalkAuthServiceProvider',

### Step 3: Configure the location of your Esotalk installation

In order to read some configuration values, the path to your Esotalk installation
needs to be configured.

To copy the package configuration file, run this command:

    php artisan config:publish modbase/esotalk-auth

You can then edit `app/config/packages/modbase/esotalk-auth/config.php`.
Change the `path` option to point to the root directory of your Esotalk
installation. Make sure it ends with a slash.

### Step 4: Enable the new authentication adapter

In your application, edit the `app/config/auth.php` file and set the `driver`
option to "esotalk", so that it looks like this:

    'driver' => 'esotalk',

## Usage

Once installed, you can use the authentication feature of Laravel as you always
do, with Laravel magically using Esotalk's database and cookie behind the scenes.

**Note**: This package will not work if your Esotalk installation uses a SQLite2 database, as this database type is not supported by Laravel.