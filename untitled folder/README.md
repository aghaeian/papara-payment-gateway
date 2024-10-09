<p align="center"><a href="https://codenteq.com" target="_blank"><img src="src/Resources/assets/images/papara.svg" width="288"></a></p>

# Papara Payment Gateway
[![License](https://poser.pugx.org/codenteq/papara-payment-gateway/license)](https://github.com/codenteq/papara-payment-gateway/blob/master/LICENSE)
[![Total Downloads](https://poser.pugx.org/codenteq/papara-payment-gateway/d/total)](https://packagist.org/packages/codenteq/papara-payment-gateway)

## 1. Introduction:

Install this package now to receive secure payments in your online store. papara offers an easy and secure payment gateway.

## 2. Requirements:

* **PHP**: 8.1 or higher.
* **Bagisto**: v2.*
* **Composer**: 1.6.5 or higher.

## 3. Installation:

- Run the following command
```
composer require codenteq/papara-payment-gateway
```

- Run these commands below to complete the setup
```
composer dump-autoload
```

> WARNING <br>
> It will check existence of the .env file, if it exists then please update the file manually with the below details.
```
PAPARA_API_KEY
PAPARA_SECRET_KEY
PAPARA_BASE_URL
```

- Run these commands below to complete the setup
```
php artisan optimize
```

## Installation without composer:

- Unzip the respective extension zip and then merge "packages" and "storage" folders into project root directory.
- Goto config/app.php file and add following line under 'providers'

```
Webkul\Papara\Providers\PaparaServiceProvider::class,
```

- Goto composer.json file and add following line under 'psr-4'

```
"Webkul\\Papara\\": "packages/Webkul/Papara/src"
```

- Run these commands below to complete the setup

```
composer dump-autoload
```

> WARNING <br>
> It will check existence of the .env file, if it exists then please update the file manually with the below details.
```
PAPARA_API_KEY
PAPARA_SECRET_KEY
PAPARA_BASE_URL
```

```
php artisan optimize
```

> That's it, now just execute the project on your specified domain.

## How to contribute
Papara Payment Gateway is always open for direct contributions. Contributions can be in the form of design suggestions, documentation improvements, new component suggestions, code improvements, adding new features or fixing problems. For more information please check our [Contribution Guideline document.](https://github.com/codenteq/papara-payment-gateway/blob/master/CONTRIBUTING.md)
