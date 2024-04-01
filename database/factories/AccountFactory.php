<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccountFactory extends Factory
{
    protected $model = Account::class;

    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'balance' => $this->faker->randomFloat(2, 100, 10000),
            'currency' => $this->faker->randomElement(['EUR', 'GBP', 'SEK']),
        ];
    }
}
