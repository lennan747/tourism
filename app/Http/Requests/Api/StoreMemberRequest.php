<?php

namespace App\Http\Requests\Api;

class StoreMemberRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
            'phone' => [
                'type'         => 'required|string',
                'captcha_key'  => 'required|string',
                'captcha_code' => 'required|string',
            ]
        ];
    }

    public function attributes()
    {
        return [
            'captcha_key' => '图片验证码 key',
            'captcha_code' => '图片验证码',
        ];
    }
}
