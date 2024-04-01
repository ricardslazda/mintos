<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Client;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class VerifyClientKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $clientKey = $request->header('x-client-key');
        $clientEmail = $request->header('x-client-email');

        if (is_null($clientKey)) {
            return response()->json(['message' => 'Client key is required.'], 403);
        }

        if (is_null($clientEmail)) {
            return response()->json(['message' => 'Client email is required.'], 403);
        }

        $client = Client::query()->where('email', $clientEmail)->first();

        if (!$client) {
            return response()->json(['message' => 'Client not found.'], 403);
        }

        $isValid = Hash::check($clientKey, $client->client_key);

        if (!$isValid) {
            return response()->json(['message' => 'Access denied. Invalid client key.'], 403);
        }

        return $next($request);
    }
}
