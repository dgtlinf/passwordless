<?php

namespace Dgtlinf\Passwordless;

use Dgtlinf\Passwordless\Events\PasswordlessLoginSucceeded;
use Dgtlinf\Passwordless\Events\PasswordlessTokenConsumed;
use Dgtlinf\Passwordless\Events\PasswordlessTokenCreated;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PasswordlessManager
{
    /**
     * Generate and send a passwordless login notification.
     */
    public function send(Authenticatable $user): mixed
    {
        $tokenModel = config('passwordless.models.token');
        $notificationClass = config('passwordless.notification.class');

        // clean old tokens for this user
        if (method_exists($user, 'invalidateTokens')) {
            $user->invalidateTokens();
        } else {
            app($tokenModel)::where('user_id', $user->getAuthIdentifier())->active()->delete();
        }

        // generate raw otp
        $otp = $this->generateOtp();

        // create new token record
        /** @var \Illuminate\Database\Eloquent\Model $token */
        $token = app($tokenModel)::create([
            'user_id'    => $user->getAuthIdentifier(),
            'token'      => hash('sha256', Str::random(config('passwordless.link.token_length', 64))),
            'otp_code'   => Hash::make($otp),
            'expires_at' => $tokenModel::generateExpiration('link'),
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
        ]);

        event(new PasswordlessTokenCreated($user, $token, $otp));

        // send notification via configured class
        $user->notify(new $notificationClass($token, $otp));

        return $token;
    }

    /**
     * Verify OTP for given user.
     */
    public function verifyOtp(Authenticatable $user, string $otp): bool
    {
        $tokenModel = config('passwordless.models.token');

        $token = method_exists($user, 'latestActiveToken')
            ? $user->latestActiveToken()
            : app($tokenModel)::where('user_id', $user->getAuthIdentifier())
                ->active()
                ->latest('created_at')
                ->first();

        if (! $token) {
            return false;
        }

        $valid = Hash::check($otp, $token->otp_code);

        if ($valid) {
            $token->consume();
            event(new PasswordlessTokenConsumed($user, $token));
            event(new PasswordlessLoginSucceeded($user));
        }

        return $valid;
    }

    /**
     * Verify magic link token (used from signed route or frontend link).
     */
    public function verifyToken(Authenticatable $user, string $rawToken): bool
    {
        $tokenModel = config('passwordless.models.token');

        $token = app($tokenModel)::where('user_id', $user->getAuthIdentifier())
            ->active()
            ->latest('created_at')
            ->first();

        if (! $token) {
            return false;
        }

        // since we store hashed token, compare via hash
        if (! hash_equals($token->token, hash('sha256', $rawToken))) {
            return false;
        }

        $token->consume();

        return true;
    }

    /**
     * Generate OTP according to config rules.
     */
    protected function generateOtp(): string
    {
        $length = config('passwordless.otp.length', 6);
        $type = config('passwordless.otp.type', 'numeric');

        return match ($type) {
            'alpha'        => Str::upper(Str::random($length)),
            'alphanumeric' => Str::upper(Str::random($length)),
            default        => str_pad((string) random_int(0, 10 ** $length - 1), $length, '0', STR_PAD_LEFT),
        };
    }
}
