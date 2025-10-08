<?php

use Dgtlinf\Passwordless\Facades\Passwordless;
use Dgtlinf\Passwordless\Models\PasswordlessToken;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;


uses(RefreshDatabase::class);

beforeEach(function () {

    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name')->nullable();
        $table->string('email')->unique();
        $table->timestamps();
    });

    include_once __DIR__ . '/../../database/migrations/create_passwordless_tokens_table.php.stub';
    (new CreatePasswordlessTokensTable())->up();

    $this->user = new class extends Authenticatable {
        use Notifiable, \Dgtlinf\Passwordless\Traits\HasPasswordlessLogin;

        protected $table = 'users';
        protected $guarded = [];

        public function getEmailForPasswordless(): string
        {
            return $this->email;
        }
    };

    $this->user = $this->user->create([
        'name' => 'Test User',
        'email' => 'user@example.com',
    ]);
});

it('creates a passwordless token and sends notification', function () {
    Notification::fake();

    expect(PasswordlessToken::count())->toBe(0);

    $token = Passwordless::send($this->user);

    expect($token)->not->toBeNull();
    expect(PasswordlessToken::count())->toBe(1);
    expect($token->user_id)->toBe($this->user->id);
    expect($token->otp_code)->not->toBeEmpty();

    Notification::assertSentTo(
        $this->user,
        config('passwordless.notification.class')
    );
});
