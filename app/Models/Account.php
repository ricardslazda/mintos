<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 *
 *
 * @property int $id
 * @property int $client_id
 * @property string $balance
 * @property string $currency
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Client $client
 * @property-read Collection<int, Transaction> $transactions
 * @property-read int|null $transactions_count
 * @method static Builder|Account currentClient(string $email)
 * @method static Builder|Account newModelQuery()
 * @method static Builder|Account newQuery()
 * @method static Builder|Account onlyTrashed()
 * @method static Builder|Account query()
 * @method static Builder|Account whereBalance($value)
 * @method static Builder|Account whereClientId($value)
 * @method static Builder|Account whereCreatedAt($value)
 * @method static Builder|Account whereCurrency($value)
 * @method static Builder|Account whereDeletedAt($value)
 * @method static Builder|Account whereId($value)
 * @method static Builder|Account whereUpdatedAt($value)
 * @method static Builder|Account withTrashed()
 * @method static Builder|Account withoutTrashed()
 */
class Account extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function scopeCurrentClient(Builder $query, string $email): Builder
    {
        return $query->whereHas('client', function (Builder $query) use ($email) {
            $query->where('email', $email);
        });
    }
}
