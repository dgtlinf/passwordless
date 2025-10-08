<?php

namespace Dgtlinf\Passwordless\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PasswordlessTokenCreated
{
    use Dispatchable, SerializesModels;

    public mixed $user;
    public mixed $token;
    public string $otp;

    public function __construct($user, $token, string $otp)
    {
        $this->user = $user;
        $this->token = $token;
        $this->otp = $otp;
    }
}
