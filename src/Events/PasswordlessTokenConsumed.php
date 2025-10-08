<?php

namespace Dgtlinf\Passwordless\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PasswordlessTokenConsumed
{
    use Dispatchable, SerializesModels;

    public mixed $user;
    public mixed $token;

    public function __construct($user, $token)
    {
        $this->user = $user;
        $this->token = $token;
    }
}
