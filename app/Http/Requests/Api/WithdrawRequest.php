<?php

namespace App\Http\Requests\Api;


class WithdrawRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->method()){
            case 'POST':
                return [
                    'application_amount' => ['required'],
                    'bank_card_id' => ['required'],
                ];
                break;
        }
    }
}
