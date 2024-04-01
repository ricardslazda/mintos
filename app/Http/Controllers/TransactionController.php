<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\GetTransactionsRequest;
use App\Http\Requests\TransferFundsRequest;
use App\Http\Services\FundsTransferService;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

class TransactionController extends Controller
{
    public function __construct(private readonly FundsTransferService $fundsTransferService)
    {
    }

    public function get(GetTransactionsRequest $request): LengthAwarePaginator|JsonResponse
    {
        $account = Account::currentClient($request->header('x-client-email'))
            ->where('id', $request->input('account_id'))
            ->first();

        /**
         * Ensure that only account owners are authorized to access transaction history.
         */
        if (!$account) {
            return response()->json(['message' => 'Account not found.'], 404);
        }

        $perPage = $request->input('limit', 10);
        $page = $request->input('page', 1);

        return Transaction::query()
            ->where('sender_account_id', $account->id)
            ->orWhere('recipient_account_id', $account->id)
            ->latest()
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public function create(TransferFundsRequest $request): JsonResponse
    {
        return $this->fundsTransferService->create($request);
    }
}
