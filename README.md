
# Passwordless Laravel Package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/dgtlinf/passwordless.svg?style=flat-square)](https://packagist.org/packages/dgtlinf/passwordless)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/dgtlinf/passwordless/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/dgtlinf/passwordless/actions)
[![License](https://img.shields.io/github/license/dgtlinf/passwordless.svg?style=flat-square)](https://github.com/dgtlinf/passwordless/blob/main/LICENSE.md)

A modern, headless, and framework-agnostic **passwordless authentication** system for Laravel.  
Easily send magic links or OTP codes without managing passwords.

---

## 🚀 Installation

```bash
composer require dgtlinf/passwordless
```

Then publish the configuration and migration with the easy install command:

```bash
php artisan passwordless:install
```

or you can just publish thing which you'd like: 

```bash
php artisan vendor:publish --tag="passwordless-config"
php artisan vendor:publish --tag="passwordless-migrations"
php artisan migrate
```

---

## ⚙️ Configuration

All configuration is handled in `config/passwordless.php`:

- **Models**: set your custom `User` model or token model
- **Notification**: customize the notification class or route
- **OTP options**: length and type (`numeric`, `alpha`, `alphanumeric`)
- **Prune**: enable auto-cleanup for expired tokens

---

## 🧩 Usage

Add the trait to your `User` model:

```php
use Dgtlinf\Passwordless\Traits\HasPasswordlessLogin;

class User extends Authenticatable
{
    use Notifiable, HasPasswordlessLogin;
}
```

Then, from any controller or service:

```php
use Dgtlinf\Passwordless\Facades\Passwordless;

Passwordless::send($user); // Sends OTP + magic link
```

To verify an OTP code:

```php
Passwordless::verifyOtp($user, $otp);
```

---

## 🌐 Translations

Default translation files are included in `resources/lang/en/passwordless.php`.  
You can publish them and create your own for other locales.

```bash
php artisan vendor:publish --tag="passwordless-translations"
```

---

## 🧪 Testing

```bash
composer test
```

---

## 🪪 License

The MIT License (MIT). See [License File](LICENSE.md) for more information.
