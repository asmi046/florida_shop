<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Client\LongLivedAccessToken;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Exceptions\InvalidArgumentException;

class TestAmo2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:amo2';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    // public function handle()
    // {

    //     $link = 'https://'.config('amo.url').'/api/v4/account';
    //     $headers = [
    //         'Authorization: Bearer ' . config('amo.long')
    //     ];

    //     $curl = curl_init(); //Сохраняем дескриптор сеанса cURL
    //     curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-oAuth-client/1.0');
    //     curl_setopt($curl,CURLOPT_URL, $link);
    //     curl_setopt($curl,CURLOPT_HTTPHEADER, $headers);
    //     curl_setopt($curl,CURLOPT_HEADER, false);
    //     curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, 1);
    //     curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, 2);
    //     $out = curl_exec($curl); //Инициируем запрос к API и сохраняем ответ в переменную
    //     $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    //     curl_close($curl);
    //     /** Теперь мы можем обработать ответ, полученный от сервера. Это пример. Вы можете обработать данные своим способом. */
    //     $code = (int)$code;
    //     $errors = [
    //         400 => 'Bad request',
    //         401 => 'Unauthorized',
    //         403 => 'Forbidden',
    //         404 => 'Not found',
    //         500 => 'Internal server error',
    //         502 => 'Bad gateway',
    //         503 => 'Service unavailable',
    //     ];

    //     try
    //     {
    //         /** Если код ответа не успешный - возвращаем сообщение об ошибке  */
    //         if ($code < 200 || $code > 204) {
    //             throw new \Exception(isset($errors[$code]) ? $errors[$code] : 'Undefined error', $code);
    //         }
    //     } catch(\Exception $e)
    //     {
    //         dd($out, );
    //         die('Ошибка: ' . $e->getMessage() . PHP_EOL . 'Код ошибки: ' . $e->getCode());
    //     }

    // }

public function handle()
{
    $link = 'https://'.config('amo.url').'/api/v4/account';
    $headers = [
        'Authorization: Bearer ' . config('amo.long')
    ];

    // Для логирования verbose информации
    $verboseLog = fopen('php://temp', 'rw+');

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-oAuth-client/1.0');
    curl_setopt($curl, CURLOPT_URL, $link);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);

    // Добавляем verbose логирование
    curl_setopt($curl, CURLOPT_VERBOSE, true);
    curl_setopt($curl, CURLOPT_STDERR, $verboseLog);

    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    // Получаем verbose лог
    rewind($verboseLog);
    $verboseLogContent = stream_get_contents($verboseLog);
    fclose($verboseLog);

    curl_close($curl);

    // Выводим информацию о запросе
    echo "=== ЗАПРОС ===" . PHP_EOL;
    echo "URL: " . $link . PHP_EOL;
    echo "Headers: " . print_r($headers, true) . PHP_EOL;
    echo "Verbose Log:" . PHP_EOL;
    echo $verboseLogContent . PHP_EOL;

    // Выводим информацию об ответе
    echo "=== ОТВЕТ ===" . PHP_EOL;
    echo "HTTP Code: " . $httpCode . PHP_EOL;
    echo "Response Body: " . $response . PHP_EOL;

    // Остальная логика обработки ошибок...
}


}
