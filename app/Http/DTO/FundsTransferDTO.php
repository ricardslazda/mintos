<?php

declare(strict_types=1);

namespace App\Http\DTO;

use App\Models\Account;

class FundsTransferDTO
{
    public Account $senderAccount;
    public Account $recipientAccount;
    public string $amountToTransfer;
    public string $senderBalanceBefore;
    public string $recipientBalanceBefore;
    public array $currencies;
}
