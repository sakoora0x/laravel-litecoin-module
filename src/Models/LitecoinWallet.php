<?php

namespace sakoora0x\LaravelLitecoinModule\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use sakoora0x\LaravelLitecoinModule\Casts\DecimalCast;

class LitecoinWallet extends Model
{
    protected $fillable = [
        'name',
        'title',
        'password',
        'sync_at',
        'balance',
        'unconfirmed_balance',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'password' => 'encrypted',
        'sync_at' => 'datetime',
        'balance' => DecimalCast::class,
        'unconfirmed_balance' => DecimalCast::class,
    ];

    public function node(): BelongsTo
    {
        /** @var class-string<LitecoinNode> $model */
        $model = config('litecoin.models.node');

        return $this->belongsTo($model, 'node_id');
    }

    public function addresses(): HasMany
    {
        /** @var class-string<LitecoinAddress> $model */
        $model = config('litecoin.models.address');

        return $this->hasMany($model, 'wallet_id', 'id');
    }

    public function deposits(): HasMany
    {
        /** @var class-string<LitecoinDeposit> $model */
        $model = config('litecoin.models.deposit');

        return $this->hasMany($model, 'wallet_id', 'id');
    }
}
