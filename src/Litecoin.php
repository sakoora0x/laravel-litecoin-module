<?php

namespace Mollsoft\LaravelLitecoinModule;

use Decimal\Decimal;
use Illuminate\Support\Facades\Date;
use Mollsoft\LaravelLitecoinModule\Models\LitecoinAddress;
use Mollsoft\LaravelLitecoinModule\Enums\AddressType;
use Mollsoft\LaravelLitecoinModule\Models\LitecoinNode;
use Mollsoft\LaravelLitecoinModule\Models\LitecoinWallet;

class Litecoin
{
    public function createNode(
        string $name,
        ?string $title,
        string $host,
        int $port = 8332,
        string $username = null,
        string $password = null
    ): LitecoinNode {
        /** @var class-string<LitecoinNode> $model */
        $model = config('litecoin.models.rpc_client');
        $api = new $model($host, $port, $username, $password);

        $api->request('getblockchaininfo');

        /** @var class-string<LitecoinNode> $model */
        $model = config('litecoin.models.node');

        return $model::create([
            'name' => $name,
            'title' => $title,
            'host' => $host,
            'port' => $port,
            'username' => $username,
            'password' => $password,
        ]);
    }

    public function createWallet(
        LitecoinNode $node,
        string $name,
        ?string $password = null,
        ?string $title = null
    ): LitecoinWallet {
        $api = $node->api();

        $api->request('createwallet', [
            'wallet_name' => $name,
            'passphrase' => $password,
            'load_on_startup' => true,
        ]);

        if ($password) {
            $api->request('walletpassphrase', [
                'passphrase' => $password,
                'timeout' => 60
            ], $name);
        }

        $wallet = $node->wallets()->create([
            'name' => $name,
            'title' => $title,
            'password' => $password,
        ]);

        $this->createAddress($wallet, null, 'Primary Address');

        return $wallet;
    }

    public function hasWallet(LitecoinNode $node, string $name): bool
    {
        $api = $node->api();
        $listWalletDir = $api->request('listwalletdir');

        foreach ($listWalletDir['wallets'] as $wallet) {
            if ($wallet['name'] === $name) {
                return true;
            }
        }

        return false;
    }

    public function loadWallet(LitecoinNode $node, string $name, ?string $password = null, ?string $title = null): LitecoinWallet
    {
        $api = $node->api();

        try {
            $api->request('loadwallet', [
                'filename' => $name,
                'load_on_startup' => true,
            ]);
        } catch (\Exception $e) {
        }

        if ($password) {
            $api->request('walletpassphrase', [
                'passphrase' => $password,
                'timeout' => 60
            ], $name);
        }

        $wallet = $node->wallets()->create([
            'name' => $name,
            'title' => $title,
            'password' => $password,
        ]);

        $listReceivedByAddress = $api->request('listreceivedbyaddress', ['include_empty' => true], $wallet->name);
        foreach ($listReceivedByAddress as $item) {
            $data = $api->request('dumpprivkey', [
                'address' => $item['address']
            ], $wallet->name);
            $privateKey = $data['result'];

            $wallet->addresses()->create([
                'address' => $item['address'],
                'type' => $this->validateAddress($node, $item['address']),
                'private_key' => $privateKey,
            ]);
        }

        if (count($listReceivedByAddress) === 0) {
            $this->createAddress($wallet, null, 'Primary Address');
        }

        return $wallet;
    }

    public function createAddress(
        LitecoinWallet $wallet,
        ?AddressType $type = null,
        ?string $title = null
    ): LitecoinAddress {
        $api = $wallet->node->api();

        if (!$type) {
            $type = config('litecoin.address_type', AddressType::BECH32);
        }

        if ($wallet->password) {
            $api->request('walletpassphrase', [
                'passphrase' => $wallet->password,
                'timeout' => 60
            ], $wallet->name);
        }

        $data = $api->request('getnewaddress', [
            'address_type' => $type->value,
        ], $wallet->name);
        $address = $data['result'];

        $data = $api->request('dumpprivkey', [
            'address' => $address
        ], $wallet->name);
        $privateKey = $data['result'];

        return $wallet->addresses()->create([
            'address' => $address,
            'type' => $type,
            'title' => $title,
            'private_key' => $privateKey,
        ]);
    }

    public function validateAddress(LitecoinNode $node, string $address): ?AddressType
    {
        $validateAddress = $node->api()->request('validateaddress', [
            'address' => $address
        ]);

        if (!($validateAddress['isvalid'] ?? false)) {
            return null;
        }

        if ($validateAddress['iswitness'] ?? false) {
            return  AddressType::BECH32;
        }
        if ($validateAddress['isscript'] ?? false) {
            return AddressType::P2SH_SEGWIT;
        }

        return AddressType::LEGACY;
    }

    public function sendAll(LitecoinWallet $wallet, string $address, int|float|null $feeRate = null): string
    {
        $api = $wallet->node->api();

        if ($wallet->password) {
            $api->request('walletpassphrase', [
                'passphrase' => $wallet->password,
                'timeout' => 60
            ], $wallet->name);
        }

        $getBalances = $api->request('getbalances', [], $wallet->name);
        $balance = new Decimal((string)$getBalances['mine']['trusted'], 8);

        return $this->send($wallet, $address, $balance, $feeRate, true);
    }

    public function send(
        LitecoinWallet $wallet,
        string $address,
        int|float|string|Decimal $amount,
        int|float|null $feeRate = null,
        bool $subtractFeeFromAmount = false
    ): string {
        $api = $wallet->node->api();

        if (($amount instanceof Decimal)) {
            $amount = new Decimal((string)$amount, 8);
        }

        if ($wallet->password) {
            $api->request('walletpassphrase', [
                'passphrase' => $wallet->password,
                'timeout' => 60
            ], $wallet->name);
        }

        $sendToAddress = $api->request('sendtoaddress', [
            'address' => $address,
            'amount' => $amount->toString(),
            'subtractfeefromamount' => $subtractFeeFromAmount,
            'estimate_mode' => $feeRate ? 'unset' : 'economical',
            'fee_rate' => $feeRate
        ], $wallet->name);

        if (!is_string($sendToAddress['result'])) {
            throw new \Exception(json_encode($sendToAddress));
        }

        return $sendToAddress['result'];
    }
}
