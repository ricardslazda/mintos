<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientsTableSeeder extends Seeder
{
    public function run(): void
    {
        $client = new Client();
        $client->email = 'mintos@mintos.com';
        $client->client_key = 'secret';
        $client->save();
    }
}
