<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BascetForm extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

     /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "fio" => ["required"],
            "phone" => [
                "required",
                "regex:/^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$/"
            ],
            "polphone" => [
                "nullable",
                "regex:/^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$/"
            ]
        ];
    }

    /**
     * Сообщения об ошибках валидации
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'phone.required' => 'Поле "Телефон" обязательно для заполнения',
            'phone.regex' => 'Телефон должен быть в формате +7 (XXX) XXX-XX-XX',
            'polphone.regex' => 'Телефон получателя должен быть в формате +7 (XXX) XXX-XX-XX',
            'fio.required' => 'Поле "ФИО" обязательно для заполнения',
        ];
    }
}
