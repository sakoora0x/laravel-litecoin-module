<?php

namespace Mollsoft\LaravelLitecoinModule\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Mollsoft\LaravelLitecoinModule\LitecoindRpcApi;

class LitecoinNode extends Model
{
    protected $fillable = [
        'name',
        'title',
        'host',
        'port',
        'username',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'port' => 'integer',
        'password' => 'encrypted',
    ];

    public function wallets(): HasMany
    {
        /** @var class-string<LitecoinWallet> $model */
        $model = config('litecoin.models.wallet');

        return $this->hasMany($model, 'node_id');
    }

    public function api(): LitecoindRpcApi
    {
        /** @var class-string<LitecoindRpcApi> $model */
        $model = config('litecoin.models.rpc_client');

        return new $model(
            host: $this->host,
            port: $this->port,
            username: $this->username,
            password: $this->password,
        );
    }
}
