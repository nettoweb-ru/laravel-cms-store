# Online store support for nettoweb CMS

This software provides online store support for nettoweb CMS.

## Installation

Change to your Laravel project directory and run: 

```shell
composer require nettoweb/laravel-cms-store
```

Apply database migrations:

```shell
php artisan migrate
```
Publish assets:

```shell
php artisan vendor:publish --tag=nettoweb-laravel-cms
```

## Licensing

This project is licensed under MIT license.
