<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferFundsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'sender_account_id' => 'required',
            'recipient_account_id' => 'required|different:sender_account_id',
            'amount' => 'required|numeric|min:0.01',
        ];
    }
}
