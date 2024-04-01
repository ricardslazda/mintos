<?php

declare(strict_types=1);

namespace App\Http\Services;

use App\Http\DTO\FundsTransferDTO;
use App\Http\Interfaces\CurrencyRateService;
use App\Http\Requests\TransferFundsRequest;
use App\Models\Account;
use App\Models\Transaction;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

readonly class FundsTransferService
{
    public function __construct(private CurrencyRateService $currencyRateService)
    {
    }

    /**
     * Warning: For accurate handling of floating-point arithmetic and precision, ensure that BCMath is utilized.
     */
    public function create(TransferFundsRequest $request): JsonResponse
    {
        try {
            [$senderAccount, $recipientAccount] = $this->getAccountsFromRequest($request);
            $currencies = $this->currencyRateService->getCurrencyRatesForUSD();

            $dto = new FundsTransferDTO();
            $dto->senderAccount = $senderAccount;
            $dto->recipientAccount = $recipientAccount;
            $dto->amountToTransfer = $request->input('amount');
            $dto->currencies = $currencies;
            $dto->senderBalanceBefore = $senderAccount->balance;
            $dto->recipientBalanceBefore = $recipientAccount->balance;

            if (!$this->areCurrenciesAvailable($dto)) {
                return response()->json(['message' => 'Currency rate not found.'], 404);
            }

            if (!$this->hasSufficientFunds($dto)) {
                return response()->json(['message' => 'Insufficient funds.'], 422);
            }

            DB::transaction(function () use ($request, $dto) {
                $this->processTransfer($dto);
                $transaction = $this->createTransaction($dto);
                $transaction->save();
            });

            return response()->json(['message' => 'Transfer successful.']);
        } catch (Exception) {
            return response()->json(['message' => 'Funds transfer failed.'], 500);
        }
    }

    /**
     * @param TransferFundsRequest $request
     * @return Account[]
     */
    private function getAccountsFromRequest(TransferFundsRequest $request): array
    {
        $senderAccount = Account::currentClient($request->header('x-client-email'))
            ->where('id', $request->input('sender_account_id'))
            ->firstOrFail();

        $recipientAccount = Account::currentClient($request->header('x-client-email'))
            ->where('id', $request->input('recipient_account_id'))
            ->firstOrFail();

        return [$senderAccount, $recipientAccount];
    }

    private function areCurrenciesAvailable(FundsTransferDTO $dto): bool
    {
        return isset($dto->currencies[$dto->senderAccount->currency], $dto->currencies[$dto->recipientAccount->currency]);
    }

    private function hasSufficientFunds(FundsTransferDTO $dto): bool
    {
        $transferAmountInAccountCurrency = $this->convertCurrency($dto->amountToTransfer, $dto->currencies[$dto->recipientAccount->currency], $dto->currencies[$dto->senderAccount->currency]);
        return bccomp($dto->senderAccount->balance, $transferAmountInAccountCurrency, 10) >= 0;
    }

    private function processTransfer(FundsTransferDTO $dto): void
    {
        $transferAmountInUSD = $this->convertCurrency($dto->amountToTransfer, $dto->currencies[$dto->recipientAccount->currency], 1);
        $dto->senderAccount->balance = bcsub($dto->senderAccount->balance, $this->convertCurrency($transferAmountInUSD, 1, $dto->currencies[$dto->senderAccount->currency]), 10);
        $dto->recipientAccount->balance = bcadd($dto->recipientAccount->balance, $this->convertCurrency($transferAmountInUSD, 1, $dto->currencies[$dto->recipientAccount->currency]), 10);

        $dto->senderAccount->save();
        $dto->recipientAccount->save();
    }

    private function createTransaction(FundsTransferDTO $dto): Transaction
    {
        return new Transaction([
            'sender_account_id' => $dto->senderAccount->id,
            'recipient_account_id' => $dto->recipientAccount->id,
            'amount' => $dto->amountToTransfer,
            'sender_balance_before' => $dto->senderBalanceBefore,
            'sender_balance_after' => $dto->senderAccount->balance,
            'recipient_balance_before' => $dto->recipientBalanceBefore,
            'recipient_balance_after' => $dto->recipientAccount->balance,
            'currency' => $dto->recipientAccount->currency,
        ]);
    }

    private function convertCurrency($amount, $fromRate, $toRate): string
    {
        return bcmul(bcdiv($amount, (string)$fromRate, 10), (string)$toRate, 10);
    }
}
