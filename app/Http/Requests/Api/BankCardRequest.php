<?php

namespace App\Http\Requests\Api;

class BankCardRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
//        return [
//            //
//            'type'      => ['required','string'],
//            'name'      => ['required','string'],
//            'card_name' => ['required','string'],
//            'account'   => ['required','string']
//        ];

        switch ($this->method()) {
            case 'POST':
                return [
                    'type' => ['required', 'string'],
                    'name' => ['required', 'string'],
                    'card_name' => ['required', 'string'],
                    'account' => ['required', 'string']
                ];
                break;
            case 'PUT':
                return [
                    'type' => ['required', 'string'],
                    'name' => ['required', 'string'],
                    'card_name' => ['required', 'string'],
                    'account' => ['required', 'string']
                ];
                break;
        }
    }
}
