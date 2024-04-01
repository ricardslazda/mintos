<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Account;
use App\Models\Client;

class AccountTableSeeder extends Seeder
{
    public function run(): void
    {
        $clients = Client::all();

        foreach ($clients as $client) {
            Account::factory()
                ->count(5)
                ->create([
                    'client_id' => $client->id,
                ]);
        }
    }
}
