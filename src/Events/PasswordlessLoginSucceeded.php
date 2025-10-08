<?php

namespace Dgtlinf\Passwordless\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PasswordlessLoginSucceeded
{
    use Dispatchable, SerializesModels;

    public mixed $user;

    public function __construct($user)
    {
        $this->user = $user;
    }
}
