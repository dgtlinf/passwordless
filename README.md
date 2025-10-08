
# 🔐 Passwordless Laravel Package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/dgtlinf/passwordless.svg?style=flat-square)](https://packagist.org/packages/dgtlinf/passwordless)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/dgtlinf/passwordless/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/dgtlinf/passwordless/actions)
[![License](https://img.shields.io/github/license/dgtlinf/passwordless.svg?style=flat-square)](https://github.com/dgtlinf/passwordless/blob/main/LICENSE.md)

A modern, headless, and framework-agnostic **passwordless authentication** system for Laravel.  
Easily send magic links and OTP codes without managing passwords.

---

## 🚀 Features

- 🔑 Passwordless login with OTP or magic link
- ⚙️ Configurable OTP length and type (numeric, alpha, alphanumeric)
- 🕒 Expiration control for tokens and magic links
- 📬 Notification-based delivery (email-ready, customizable channel)
- 🧩 Event-driven: `PasswordlessTokenCreated`, `PasswordlessTokenConsumed`, `PasswordlessLoginSucceeded`
- 🌍 Localization-ready (translations publishable)
- 🧪 100% test coverage with Pest + Orchestra Testbench
- 🧱 Fully decoupled — no frontend required

---

## ⚙️ Installation

```bash
composer require dgtlinf/passwordless
```

Then publish the configuration and migration with the easy install command:

```bash
php artisan passwordless:install
```

or you can just publish things separately:

```bash
php artisan vendor:publish --tag="passwordless-config"
php artisan vendor:publish --tag="passwordless-migrations"
php artisan migrate
```

---

## 🧩 Configuration

File: `config/passwordless.php`

### Key sections:

#### `models`
Define which models to use.  
You can override both the User model and the Token model.

```php
'models' => [
    'user'  => App\Models\User::class,
    'token' => Dgtlinf\Passwordless\Models\PasswordlessToken::class,
],
```

#### `notification`
Define which notification class is used to deliver OTPs or links, and which route name generates the magic link.

```php
'notification' => [
    'class' => Dgtlinf\Passwordless\Notifications\PasswordlessLoginNotification::class,
    'route' => 'passwordless.magic', // used by URL::temporarySignedRoute()
],
```

#### `otp`
Configure OTP behavior.

```php
'otp' => [
    'length' => 6,
    'type'   => 'numeric', // 'alpha' | 'alphanumeric' | 'numeric'
],
```

#### `link`
Configure token expiration for the magic link.

```php
'link' => [
    'expire_minutes' => 30,
    'token_length'   => 64,
],
```

#### `prune`
Automatic cleanup for old tokens (via Laravel’s model pruning).

```php
'prune' => [
    'enabled' => true,
    'after_days' => 7,
],
```

---

## 🧠 Usage

### 1️⃣ Add the trait to your User model

```php
use Dgtlinf\Passwordless\Traits\HasPasswordlessLogin;

class User extends Authenticatable
{
    use Notifiable, HasPasswordlessLogin;
}
```

### 2️⃣ Trigger passwordless login

```php
use Dgtlinf\Passwordless\Facades\Passwordless;

Passwordless::send($user); // Sends OTP + magic link
```

### 3️⃣ Verify OTP or token

```php
Passwordless::verifyOtp($user, $otp);     // via code
Passwordless::verifyToken($user, $token); // via magic link
```

---

## 🧩 Events

The following events are dispatched automatically and can be listened to in your app:

| Event | Description |
|-------|--------------|
| `PasswordlessTokenCreated` | Fired when a token and OTP are generated |
| `PasswordlessTokenConsumed` | Fired when a token is successfully used |
| `PasswordlessLoginSucceeded` | Fired when login completes successfully |

Example listener registration (`EventServiceProvider`):

```php
protected $listen = [
    Dgtlinf\Passwordless\Events\PasswordlessLoginSucceeded::class => [
        App\Listeners\LogPasswordlessLogin::class,
    ],
];
```

---

## 🌍 Translations

Translations are stored in `resources/lang/vendor/passwordless/en/passwordless.php` and can be published via:

```bash
php artisan vendor:publish --tag="passwordless-translations"
```

---

## 🧪 Testing

Run test suite:

```bash
vendor/bin/pest
```

All tests run using in-memory SQLite and are automatically configured via Orchestra Testbench.

---

## 🧾 License

Released under the **MIT License**.  
Copyright © Digital Infinity DOO Novi Sad.

**Website:** [digitalinfinity.rs](https://www.digitalinfinity.rs)
