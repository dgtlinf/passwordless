<?php

use Dgtlinf\Passwordless\Events\PasswordlessLoginSucceeded;
use Dgtlinf\Passwordless\Events\PasswordlessTokenConsumed;
use Dgtlinf\Passwordless\Events\PasswordlessTokenCreated;
use Dgtlinf\Passwordless\Facades\Passwordless;
use Dgtlinf\Passwordless\Models\PasswordlessToken;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

beforeEach(function () {
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('email')->unique();
        $table->timestamps();
    });

    include_once __DIR__ . '/../../database/migrations/create_passwordless_tokens_table.php.stub';
    (new CreatePasswordlessTokensTable())->up();

    $this->user = new class extends Authenticatable {
        use Notifiable, \Dgtlinf\Passwordless\Traits\HasPasswordlessLogin;
        protected $table = 'users';
        protected $guarded = [];
    };

    $this->user = $this->user->create([
        'email' => 'events@example.com',
    ]);
});

it('dispatches PasswordlessTokenCreated event on send', function () {
    Event::fake();

    Passwordless::send($this->user);

    Event::assertDispatched(PasswordlessTokenCreated::class);
});

it('dispatches PasswordlessTokenConsumed and LoginSucceeded on otp verification', function () {
    Event::fake([PasswordlessTokenConsumed::class, PasswordlessLoginSucceeded::class]);

    $otp = '123456';
    PasswordlessToken::create([
        'user_id' => $this->user->id,
        'token' => hash('sha256', 'token'),
        'otp_code' => Hash::make($otp),
        'expires_at' => now()->addMinutes(5),
    ]);

    Passwordless::verifyOtp($this->user, $otp);

    Event::assertDispatched(PasswordlessTokenConsumed::class);
    Event::assertDispatched(PasswordlessLoginSucceeded::class);
});
