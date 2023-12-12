<?php

namespace Mollsoft\LaravelLitecoinModule\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Mollsoft\LaravelLitecoinModule\Casts\DecimalCast;

class LitecoinDeposit extends Model
{
    protected $fillable = [
        'wallet_id',
        'address_id',
        'txid',
        'amount',
        'block_height',
        'confirmations',
        'time_at',
    ];

    protected $casts = [
        'amount' => DecimalCast::class,
        'confirmations' => 'integer',
        'time_at' => 'datetime',
    ];

    public function wallet(): BelongsTo
    {
        /** @var class-string<LitecoinWallet> $model */
        $model = config('litecoin.models.wallet');

        return $this->belongsTo($model, 'wallet_id');
    }

    public function address(): BelongsTo
    {
        /** @var class-string<LitecoinAddress> $model */
        $model = config('litecoin.models.address');

        return $this->belongsTo($model, 'address_id');
    }
}
