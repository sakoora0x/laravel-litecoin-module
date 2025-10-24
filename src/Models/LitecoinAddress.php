<?php

namespace sakoora0x\LaravelLitecoinModule\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use sakoora0x\LaravelLitecoinModule\Casts\DecimalCast;
use sakoora0x\LaravelLitecoinModule\Enums\AddressType;

class LitecoinAddress extends Model
{
    protected $fillable = [
        'wallet_id',
        'address',
        'type',
        'title',
        'private_key',
        'sync_at',
        'balance',
        'unconfirmed_balance'
    ];

    protected $hidden = [
        'encrypted'
    ];

    protected $casts = [
        'type' => AddressType::class,
        'private_key' => 'encrypted',
        'sync_at' => 'datetime',
        'balance' => DecimalCast::class,
        'unconfirmed_balance' => DecimalCast::class,
    ];

    public function wallet(): BelongsTo
    {
        /** @var class-string<LitecoinWallet> $model */
        $model = config('litecoin.models.wallet');

        return $this->belongsTo($model, 'wallet_id', 'id');
    }

    public function deposits(): HasMany
    {
        /** @var class-string<LitecoinDeposit> $model */
        $model = config('litecoin.models.deposit');

        return $this->hasMany($model, 'address_id', 'id');
    }
}
