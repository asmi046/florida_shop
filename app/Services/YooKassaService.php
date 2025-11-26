<?php
namespace App\Services;

use YooKassa\Client;
use App\Models\Order;
use App\Actions\TelegramSendAction;
use Illuminate\Support\Facades\Log;
use YooKassa\Model\Notification\NotificationFactory;
use YooKassa\Model\Notification\NotificationEventType;

class YooKassaService {



    public function pay_fixation() {

        try {
            $source = file_get_contents('php://input');
            $data = json_decode($source, true);

            $factory = new NotificationFactory();
            $notificationObject = $factory->factory($data);
            $responseObject = $notificationObject->getObject();

            $client = new \YooKassa\Client();

            if (!config('yookassa.shop_id')) {
                if (!$client->isNotificationIPTrusted($_SERVER['REMOTE_ADDR'])) {
                    header('HTTP/1.1 400 Something went wrong (IP)');
                    exit();
                }
            }


            if ($notificationObject->getEvent() === NotificationEventType::PAYMENT_SUCCEEDED) {
                $someData = array(
                    'paymentId' => $responseObject->getId(),
                    'paymentStatus' => $responseObject->getStatus(),
                );
                Log::channel('pay')->info('Payment succeeded: '. $responseObject->getId());

            }  elseif ($notificationObject->getEvent() === NotificationEventType::PAYMENT_CANCELED) {
                $someData = array(
                    'paymentId' => $responseObject->getId(),
                    'paymentStatus' => $responseObject->getStatus(),
                );
                Log::channel('pay')->info('Payment concled: '. $responseObject->getId());
            }
            else {
                header('HTTP/1.1 400 Something went wrong (Type)');
                exit();
            }


            $client->setAuth(config('yookassa.shop_id'), config('yookassa.secret_key'));

            if ($paymentInfo = $client->getPaymentInfo($someData['paymentId'])) {
                $paymentStatus = $paymentInfo->getStatus();
                $order = Order::where('pay_order', $someData['paymentId'])->first();
                if ($order) {
                    $order->pay_status = 1;
                    $order->pay_status_text = $someData['paymentStatus'];
                    $order->save();

                        $orderStatusText = ($someData['paymentStatus'] === "succeeded")?"Оплачен":"Не оплачен";

                        $pay_text = "<b>Заказ#".$someData['paymentId']." ".$orderStatusText." </b>\n\r";
                        $pay_text .= "<b>ID Сбера: </b>".$someData['paymentId']."\n\r";
                        $pay_text .= "<b>Сумма: </b>".floatval($paymentInfo["amount"]["value"])." ₽\n\r";
                        $tgsender = new TelegramSendAction();
                        $tgsender->handle($pay_text);


                    Log::channel('pay')->info('Order status updated info: '. print_r($paymentInfo, true));
                    Log::channel('pay')->info('Order status updated: '. $order->id);

                } else {
                    Log::channel('pay')->info('Order not found: '. $someData['paymentId']);
                }
            } else {
                header('HTTP/1.1 400 Something went wrong (info)');
                exit();
            }

        } catch (Exception $e) {
            header('HTTP/1.1 400 Something went wrong (all)');
            exit();
        }
    }

    protected function get_receipt($order, $tovars){
        $receipt = [
            'tax_system_code' => 6,
            'items' => [],
            'customer' => [
                'full_name' => $order->name,
                'email' => $order->email,
                'phone' => phone_format($order->phone),
            ],
        ];


        foreach ($tovars as $product) {
            $receipt['items'][] = [
                'description' => $product['tovar_data']['title'],
                'quantity' => $product['quentity'],
                'vat_code' => 1,
                'payment_subject' => 'commodity',
                'payment_mode' => 'full_prepayment',
                'country_of_origin_code' => 'RU',
                'amount' => [
                    'value' => $product['price'].'.00',
                    'currency' => 'RUB',
                ]
            ];
        }

        return $receipt;
    }

    public function getOrderStatus(string $pay_id) {
        Log::channel('pay')->info('Get pay order staus: '. $pay_id);

        $client = new Client();
        $client->setAuth(config('yookassa.shop_id'), config('yookassa.secret_key'));

        try {
            return $client->getPaymentInfo($pay_id);
        } catch (\Exception $e) {
            Log::channel('pay')->error('Get pay order staus FAILD: '. $e->getMessage());
            return null;
        }
    }

    public function registerOrder(Order $order, array $tovars) {
        Log::channel('pay')->info('Pay create: '. $order->id);

        $client = new Client();
        $client->setAuth(config('yookassa.shop_id'), config('yookassa.secret_key'));

        $pay_info = array(
            'amount' => array(
                'value' => $order->amount.'.00',
                'currency' => 'RUB',
            ),
            'receipt' => $this->get_receipt($order, $tovars),
            'confirmation' => array(
                'type' => 'redirect',
                'return_url' => route("bascet_thencs").'?order_id='.$order->id,
            ),
            'capture' => true,
            'description' => 'Заказ №'.$order->id,
            'metadata' => [
                'order_id' => $order->id
            ]
        );

        $payment = $client->createPayment(
            $pay_info,
            uniqid('', true)
        );

        return $payment;
    }

}
