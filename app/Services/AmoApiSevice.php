<?php
namespace App\Services;

use AmoCRM\Models\LeadModel;

use AmoCRM\Models\ContactModel;
use App\Http\Requests\BascetForm;
use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Client\LongLivedAccessToken;
use App\Actions\BascetBodyToTextAction;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Exceptions\InvalidArgumentException;
use AmoCRM\Models\CustomFieldsValues\MultitextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueModels\MultitextCustomFieldValueModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\MultitextCustomFieldValueCollection;

class AmoApiSevice {
    public function create_order(BascetForm $request, $order_number = "") {

        $apiClient = new AmoCRMApiClient();
        try {
            $longLivedAccessToken = new LongLivedAccessToken(config('amo.long'));
        } catch (InvalidArgumentException $e) {
            printError($e);
            die;
        }

        $apiClient->setAccessToken($longLivedAccessToken)
            ->setAccountBaseDomain(config('amo.url'));

        try {
            $leadsService = $apiClient->leads();
            $lead = new LeadModel();
            $lead->setName('Название сделки')
            ->setName('Заказ с сайта №'. $order_number)
            ->setPrice($request->input("amount"));

            $lead = $leadsService->addOne($lead);

        } catch (AmoCRMApiException $e) {
            var_dump($e->getTraceAsString());
            printError($e);
            die;
        }

    }

}
