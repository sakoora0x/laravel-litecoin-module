# Laravel Litecoin Module

Organization of payment acceptance and automation of payments of LTC coins on the Litecoin blockchain.

### Installation
You can install the package via composer:
```bash
composer require mollsoft/laravel-litecoin-module
```

After you can run installer using command:
```bash
php artisan litecoin:install
```

And run migrations:
```bash
php artisan migrate
```

Register Service Provider and Facade in app, edit `config/app.php`:
```php
'providers' => ServiceProvider::defaultProviders()->merge([
    ...,
    \Mollsoft\LaravelLitecoinModule\LitecoinServiceProvider::class,
])->toArray(),

'aliases' => Facade::defaultAliases()->merge([
    ...,
    'Litecoin' => \Mollsoft\LaravelLitecoinModule\Facades\Litecoin::class,
])->toArray(),
```