<?php

namespace App\Http\Requests\Api;


class UserRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // 邀请码以为空，存在就必须存在在users表中的id
            'invite_code'         => [
                'nullable',
                'exists:invite_codes,code'
            ],
            'password'          => 'required|string|min:6',
            'verification_key'  => 'required|string',
            'verification_code' => 'required|string',
        ];
    }

    public function attributes()
    {
        return [
            'verification_key' => '短信验证码 key',
            'verification_code' => '短信验证码'
        ];
    }
}
