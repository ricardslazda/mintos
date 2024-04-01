<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetTransactionsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'account_id' => 'required',
            'limit' => 'sometimes|integer|min:1',
            'page' => 'sometimes|integer|min:1',
        ];
    }
}
