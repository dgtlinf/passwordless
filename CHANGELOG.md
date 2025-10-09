# Changelog

All notable changes to this project will be documented in this file.

## v1.0.1 - 2025-10-09

### v1.0.1 — Documentation Update

This update improves the documentation for the **dgtlinf/passwordless** package.

#### ✍️ What's new

- Added full README documentation:
  - Detailed configuration examples
  - Usage instructions for OTP and magic links
  - Event list (`PasswordlessTokenCreated`, `PasswordlessTokenConsumed`, `PasswordlessLoginSucceeded`)
  - Translation and pruning details
  - CI, tests, and setup instructions
  
- Polished structure and formatting for GitHub and Packagist display

No code or behavioral changes were introduced — functionality remains identical to v1.0.0.


---

**Maintained by:** Digital Infinity DOO Novi Sad
**Website:** [digitalinfinity.rs](https://www.digitalinfinity.rs)

## v1.0.0 - 2025-10-08

### v1.0.0 — Initial Stable Release

This is the first stable release of **dgtlinf/passwordless**, a headless, test-driven Laravel package for passwordless authentication.

#### 🚀 Highlights

- Full passwordless login flow (OTP + magic link)
- Configurable OTP length and type (numeric, alpha, alphanumeric)
- Magic link route with configurable expiration
- Extensible model + trait architecture
- Built-in events for token creation, consumption, and login success
- Notification-based delivery (email-ready, customizable channels)
- Translation-ready (supports Laravel localization)
- 100% test coverage with Pest + Orchestra Testbench

#### 🧩 Requirements

- PHP 8.2+
- Laravel 10, 11, or 12

#### 🧪 Tested

CI tested via GitHub Actions across PHP 8.2/8.3 and Laravel 10–12.


---

**Maintained by:** Digital Infinity DOO Novi Sad
**Website:** [digitalinfinity.rs](https://www.digitalinfinity.rs)

## [Unreleased]

- Initial setup and scaffolding.
