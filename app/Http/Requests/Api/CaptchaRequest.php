<?php

namespace App\Http\Requests\Api;

class CaptchaRequest extends FormRequest
{
    public function rules()
    {

        switch ($this->input('type'))
        {
            case 'register':
                return [
                    'phone' => [
                        'required',
                        'regex:/^((13[0-9])|(14[5,7])|(15[0-3,5-9])|(17[0,3,5-8])|(18[0-9])|166|198|199)\d{8}$/',
                        'unique:users'
                    ]
                ];
                break;
            case 'reset_password':
                return [
                    'phone' => [
                        'required',
                        'regex:/^((13[0-9])|(14[5,7])|(15[0-3,5-9])|(17[0,3,5-8])|(18[0-9])|166|198|199)\d{8}$/',
                        'exists:users,phone'
                    ]
                ];
                break;

        }
    }

    public function messages()
    {
        return [
            'phone.unique' => '该手机号已经注册',
            'phone.exists' => '该手机号不存在'
        ];
    }
}
