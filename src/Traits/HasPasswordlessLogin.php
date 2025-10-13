<?php
namespace Dgtlinf\Passwordless\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use RuntimeException;

trait HasPasswordlessLogin
{
    /**
     * Boot method: verify that the model uses Notifiable.
     */
    protected static function bootHasPasswordlessLogin(): void
    {
        if (! in_array(Notifiable::class, class_uses_recursive(static::class))) {
            throw new RuntimeException(sprintf(
                'The model [%s] must use the Notifiable trait to support passwordless login notifications.',
                static::class
            ));
        }
    }

    /**
     * Relation to the PasswordlessToken model defined in config.
     */
    public function passwordlessTokens(): HasMany
    {
        $model = config('passwordless.models.token');

        return $this->hasMany($model, 'user_id');
    }

    /**
     * Get the email address (or other identifier) used for passwordless login.
     *
     * This method can be overridden in the User model if your authentication
     * uses a different field (e.g., 'username', 'contact_email', etc.).
     */
    public function getEmailForPasswordless(): string
    {
        return $this->email;
    }

    /**
     * Helper: delete all tokens for this user.
     */
    public function invalidatePasswordlessTokens(): void
    {
        $this->passwordlessTokens()->delete();
    }

    /**
     * Helper: get latest active token (for inspection or custom checks).
     */
    public function latestActivePasswordlessToken()
    {
        return $this->passwordlessTokens()
            ->active()
            ->latest('created_at')
            ->first();
    }

}
