<?php

namespace Mollsoft\LaravelLitecoinModule\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Mollsoft\LaravelLitecoinModule\Models\LitecoinDeposit;
use Mollsoft\LaravelLitecoinModule\WebhookHandlers\WebhookHandlerInterface;

class LitecoinWebhookCommand extends Command
{
    protected $signature = 'litecoin:webhook {deposit_id}';

    protected $description = 'Litecoin deposit webhook handler';

    public function handle(): void
    {
        /** @var class-string<LitecoinDeposit> $model */
        $model = config('litecoin.models.deposit');
        $deposit = $model::with(['wallet', 'address'])->findOrFail($this->argument('deposit_id'));

        /** @var class-string<WebhookHandlerInterface> $model */
        $model = config('litecoin.webhook_handler');

        /** @var WebhookHandlerInterface $handler */
        $handler = App::make($model);

        $handler->handle($deposit->wallet, $deposit->address, $deposit);

        $this->info('Webhook successfully execute!');
    }
}
