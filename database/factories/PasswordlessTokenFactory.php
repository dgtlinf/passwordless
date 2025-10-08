<?php

namespace Dgtlinf\Passwordless\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PasswordlessTokenFactory extends Factory
{
    /**
     * Dynamically resolve model from config.
     */
    public function __construct(...$args)
    {
        parent::__construct(...$args);

        $this->model = config('passwordless.models.token');
    }

    public function definition(): array
    {
        $userModel = config('passwordless.user_model');

        return [
            'user_id' => $userModel::query()->inRandomOrder()->value('id') ?? $userModel::factory(),
            'token' => hash('sha256', Str::random(64)),
            'otp_code' => (string) random_int(100000, 999999),
            'expires_at' => now()->addMinutes(config('passwordless.link.expires_in', 30)),
            'consumed_at' => null,
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
        ];
    }

    /**
     * Expired token state.
     */
    public function expired(): static
    {
        return $this->state(fn() => [
            'expires_at' => now()->subMinutes(10),
        ]);
    }

    /**
     * Consumed token state.
     */
    public function consumed(): static
    {
        return $this->state(fn() => [
            'consumed_at' => now(),
        ]);
    }

    /**
     * Active token state (valid & unconsumed).
     */
    public function active(): static
    {
        return $this->state(fn() => [
            'expires_at' => now()->addMinutes(30),
            'consumed_at' => null,
        ]);
    }
}
