<?php

return [

    /*
    |--------------------------------------------------------------------------
    | User Model
    |--------------------------------------------------------------------------
    |
    | Define which Eloquent model represents your application's users.
    | The model must implement the "Illuminate\Contracts\Auth\Authenticatable"
    | contract and use the "Illuminate\Notifications\Notifiable" trait so
    | that it can receive notifications such as OTP or magic links.
    |
    | Example: App\Models\User::class
    |
    */
    'user_model' => App\Models\User::class,

    /*
    |--------------------------------------------------------------------------
    | Model Bindings
    |--------------------------------------------------------------------------
    |
    | Define the models used internally by the package. This allows you to
    | extend or replace them with your own implementations. For example,
    | you might use ULIDs, UUIDs, or additional attributes for tracking.
    |
    | The default PasswordlessToken model stores OTP and magic link data.
    |
    */
    'models' => [
        'token' => Dgtlinf\Passwordless\Models\PasswordlessToken::class,
        'table' => 'passwordless_tokens',
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Configuration
    |--------------------------------------------------------------------------
    |
    | Defines how the notification will be sent when a passwordless login
    | is initiated. You can override this class with your own implementation
    | that supports multiple channels (Mail, SMS, Slack, etc.).
    |
    | "class"  - fully qualified class name of the notification
    | "route"  - route name used to generate the magic link URL (if applicable)
    |
    */
    'notification' => [
        'class' => Dgtlinf\Passwordless\Notifications\PasswordlessLoginNotification::class,
        'route' => 'passwordless.magic', // developer should set this to their frontend or API route
    ],


    /*
    |--------------------------------------------------------------------------
    | OTP (One-Time Passcode) Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for the OTP system. The OTP code will be generated when a user
    | requests to sign in and can be used instead of a magic link.
    |
    | length: The number of characters to generate.
    | type: The character set for the OTP. Options:
    |       - numeric (e.g. 839201)
    |       - alpha (e.g. YGKPAQ)
    |       - alphanumeric (e.g. A9X4D2)
    | expires_in: Duration in minutes before the OTP becomes invalid.
    |
    */
    'otp' => [
        'length' => 6,
        'type' => 'numeric', // numeric | alpha | alphanumeric
        'expires_in' => 30, // minutes
    ],

    /*
    |--------------------------------------------------------------------------
    | Magic Link Configuration
    |--------------------------------------------------------------------------
    |
    | Magic links provide passwordless authentication via a temporary link.
    | The user can click the link instead of entering an OTP code.
    |
    | expires_in: Link validity in minutes.
    | token_length: Length of the generated token (hashed before storage).
    |
    */
    'link' => [
        'expires_in' => 30,
        'token_length' => 64,
    ],


    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Prevents abuse of OTP or magic link requests by limiting how many times
    | a user can request a new code within a certain time frame.
    |
    | max_requests: Maximum allowed requests per decay period.
    | decay_minutes: Duration of the limit window in minutes.
    |
    */
    'rate_limit' => [
        'max_requests' => 3,
        'decay_minutes' => 10,
    ],

    /*
    |--------------------------------------------------------------------------
    | Automatic Pruning
    |--------------------------------------------------------------------------
    |
    | If enabled, the PasswordlessToken model will be automatically included
    | in Laravel's model pruning process. You can control pruning behavior
    | using the "php artisan model:prune" command or via scheduled tasks.
    |
    | "enabled" - toggles pruning
    | "keep_days" - defines how many days to retain old records
    |
    */
    'prune' => [
        'enabled' => true,
        'keep_days' => 7,
    ],
];
