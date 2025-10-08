<?php

use Dgtlinf\Passwordless\Facades\Passwordless;
use Dgtlinf\Passwordless\Models\PasswordlessToken;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

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
        'email' => 'verify@example.com',
    ]);
});

it('verifies valid otp and consumes token', function () {
    // 1. kreiraj fake token ručno
    $otp = '123456';
    $token = PasswordlessToken::create([
        'user_id' => $this->user->id,
        'token' => hash('sha256', 'dummy'),
        'otp_code' => Hash::make($otp),
        'expires_at' => now()->addMinutes(10),
    ]);

    // 2. pozovi verifyOtp()
    $valid = Passwordless::verifyOtp($this->user, $otp);

    expect($valid)->toBeTrue()
        ->and($token->fresh()->consumed_at)->not->toBeNull();
});

it('fails to verify invalid otp', function () {
    $otp = '123456';
    PasswordlessToken::create([
        'user_id' => $this->user->id,
        'token' => hash('sha256', 'dummy'),
        'otp_code' => Hash::make($otp),
        'expires_at' => now()->addMinutes(10),
    ]);

    $invalid = Passwordless::verifyOtp($this->user, '999999');
    expect($invalid)->toBeFalse();
});

it('verifies magic link token correctly', function () {
    $raw = 'supersecrettoken';
    $token = PasswordlessToken::create([
        'user_id' => $this->user->id,
        'token' => hash('sha256', $raw),
        'otp_code' => Hash::make('888888'),
        'expires_at' => now()->addMinutes(10),
    ]);

    $valid = Passwordless::verifyToken($this->user, $raw);

    expect($valid)->toBeTrue()
        ->and($token->fresh()->consumed_at)->not->toBeNull();
});

