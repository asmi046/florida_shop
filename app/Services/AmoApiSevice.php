<?php
namespace App\Services;

use AmoCRM\Models\LeadModel;

use AmoCRM\Models\ContactModel;
use App\Http\Requests\BascetForm;
use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Client\LongLivedAccessToken;
use App\Actions\BascetBodyToTextAction;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Collections\ContactsCollection;
use AmoCRM\Exceptions\InvalidArgumentException;
use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Models\CustomFieldsValues\DateCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\TextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\MultitextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueModels\DateCustomFieldValueModel;
use AmoCRM\Models\CustomFieldsValues\ValueModels\TextCustomFieldValueModel;
use AmoCRM\Models\CustomFieldsValues\ValueModels\MultitextCustomFieldValueModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\DateCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\TextCustomFieldValueCollection;
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
            $contact = new ContactModel();
            $contact->setName($request->input("fio") ?? 'Без имени');

            $customFieldsContact = new CustomFieldsValuesCollection();

            $phoneField = (new MultitextCustomFieldValuesModel())->setFieldCode('PHONE');
            $phoneField->setValues(
                (new MultitextCustomFieldValueCollection())
                    ->add(
                        (new MultitextCustomFieldValueModel())
                            ->setValue($request->input("phone"))
                    )
            );
            $customFieldsContact->add($phoneField);

            $emailField = (new MultitextCustomFieldValuesModel())->setFieldCode('EMAIL');
            $emailField->setValues(
                (new MultitextCustomFieldValueCollection())
                    ->add(
                        (new MultitextCustomFieldValueModel())
                            ->setEnum('WORK')
                            ->setValue($request->input("email") ?? 'example@test.com')
                    )
            );
            $customFieldsContact->add($emailField);


            $contact->setCustomFieldsValues($customFieldsContact);
            $contactModel = $apiClient->contacts()->addOne($contact);
        } catch (AmoCRMApiException $e) {
            \Log::error('AmoCRMApiException (contact): ' . $e->getMessage());
        }

        try {
            $leadsService = $apiClient->leads();
            $lead = new LeadModel();
            $lead->setName('Название сделки')
            ->setName('Заказ с сайта '. $order_number)
            ->setPrice($request->input("amount"))->setContacts(
                (new ContactsCollection())
                    ->add(
                        (new ContactModel())
                            ->setId( $contactModel->getId())
                    )
            );

            $leadCustomFieldsValues = new CustomFieldsValuesCollection();

            $cfOrderNumber = new TextCustomFieldValuesModel();
            $cfOrderNumber->setFieldId(847887);
            $cfOrderNumber->setValues(
                (new TextCustomFieldValueCollection())
                    ->add((new TextCustomFieldValueModel())->setValue($order_number))
            );
            $leadCustomFieldsValues->add($cfOrderNumber);


            $cfPolName = new TextCustomFieldValuesModel();
            $cfPolName->setFieldId(658271);
            $cfPolName->setValues(
                (new TextCustomFieldValueCollection())
                    ->add((new TextCustomFieldValueModel())->setValue($request->input("polname") ?? ''))
            );
            $leadCustomFieldsValues->add($cfPolName);

            $cfPolPhone = new TextCustomFieldValuesModel();
            $cfPolPhone->setFieldId(658273);
            $cfPolPhone->setValues(
                (new TextCustomFieldValueCollection())
                    ->add((new TextCustomFieldValueModel())->setValue($request->input("polphone") ?? ''))
            );
            $leadCustomFieldsValues->add($cfPolPhone);


            $cfDelAdress = new TextCustomFieldValuesModel();
            $cfDelAdress->setFieldId(658267);
            $cfDelAdress->setValues(
                (new TextCustomFieldValueCollection())
                    ->add((new TextCustomFieldValueModel())->setValue(
                        (
                            $request->input("delivery")."  Подъезд:".
                            $request->input("podezd")." Этаж:".
                            $request->input("etazg")." Квартира:".
                            $request->input("kvartira")
                        ) ?? ''))
            );
            $leadCustomFieldsValues->add($cfDelAdress);

            if ($request->input("data")) {
                $cfDelData = new DateCustomFieldValuesModel();
                $cfDelData->setFieldId(658215);
                $cfDelData->setValues(
                    (new DateCustomFieldValueCollection())
                        ->add((new DateCustomFieldValueModel())->setValue(date('Y-m-d', strtotime($request->input("data")))))
                );
                $leadCustomFieldsValues->add($cfDelData);
            }

            if ($request->input("time")) {
                $cfDelTime = new TextCustomFieldValuesModel();
                $cfDelTime->setFieldId(847895);
                $cfDelTime->setValues(
                    (new TextCustomFieldValueCollection())
                        ->add((new TextCustomFieldValueModel())->setValue($request->input("time") ?? ''))
                );
                $leadCustomFieldsValues->add($cfDelTime);
            }

            $cfComment = new TextCustomFieldValuesModel();
            $cfComment->setFieldId(658283);
            $cfComment->setValues(
                (new TextCustomFieldValueCollection())
                    ->add((new TextCustomFieldValueModel())->setValue($request->input("comment") ?? ''))
            );
            $leadCustomFieldsValues->add($cfComment);

            $to_text = new BascetBodyToTextAction();
            $bascet_body = $to_text->handle($request, false);

            $cfBody = new TextCustomFieldValuesModel();
            $cfBody->setFieldId(837739);
            $cfBody->setValues(
                (new TextCustomFieldValueCollection())
                    ->add((new TextCustomFieldValueModel())->setValue($bascet_body ?? ''))
            );
            $leadCustomFieldsValues->add($cfBody);

            $lead->setCustomFieldsValues($leadCustomFieldsValues);

            $lead = $leadsService->addOne($lead);

        } catch (AmoCRMApiException $e) {
            \Log::error('AmoCRMApiException (lead): ' . $e->getMessage());
            abort(500, 'Ошибка при создании сделки: ' . $e->getMessage());
        }

    }

}
