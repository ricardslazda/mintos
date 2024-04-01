<?php

declare(strict_types=1);

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 *
 *
 * @property int $id
 * @property int $sender_account_id
 * @property int $recipient_account_id
 * @property string $amount
 * @property string $sender_balance_before
 * @property string $sender_balance_after
 * @property string $recipient_balance_before
 * @property string $recipient_balance_after
 * @property string $currency
 * @property-read Account|null $account
 * @method static Builder|Transaction newModelQuery()
 * @method static Builder|Transaction newQuery()
 * @method static Builder|Transaction query()
 * @method static Builder|Transaction whereAmount($value)
 * @method static Builder|Transaction whereCurrency($value)
 * @method static Builder|Transaction whereId($value)
 * @method static Builder|Transaction whereRecipientAccountId($value)
 * @method static Builder|Transaction whereRecipientBalanceAfter($value)
 * @method static Builder|Transaction whereRecipientBalanceBefore($value)
 * @method static Builder|Transaction whereSenderAccountId($value)
 * @method static Builder|Transaction whereSenderBalanceAfter($value)
 * @method static Builder|Transaction whereSenderBalanceBefore($value)
 * @method static Builder|Transaction whereTransactionDate($value)
 * @mixin Eloquent
 */
class Transaction extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
}
