<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Client\LongLivedAccessToken;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Exceptions\InvalidArgumentException;

class TestAmo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:amo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $apiClient = new AmoCRMApiClient();
        try {
            $longLivedAccessToken = new LongLivedAccessToken(config('amo.long'));
        } catch (InvalidArgumentException $e) {
            printError($e);
            die;
        }

        $apiClient->setAccessToken($longLivedAccessToken)
            ->setAccountBaseDomain(config('amo.url'));

        //Получим информацию об аккаунте
        try {
            $account = $apiClient->account()->getCurrent();
        } catch (AmoCRMApiException $e) {
            var_dump($e->getTraceAsString());
            printError($e);
            die;
        }

        echo $account->getName();
    //     $apiClient = new AmoCRMApiClient(config('amo.id'), config('amo.secret'), "https://florida46.ru/");
        // $apiClient = new AmoCRMApiClient(config('amo.id'), config('amo.secret'), "https://florida46.ru/");
        // $state = bin2hex(random_bytes(16));
        // $authorizationUrl = $apiClient->getOAuthClient()->getAuthorizeUrl([
        //     'state' => $state,
        //     'mode' => 'post_message',
        // ]);
        // dd($authorizationUrl);
    }
}
