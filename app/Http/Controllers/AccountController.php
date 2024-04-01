<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    /**
     * @param Request $request
     * @return Account[]|Collection
     */
    public function __invoke(Request $request): array|Collection
    {
        return Account::currentClient($request->header('x-client-email'))->get();
    }
}
