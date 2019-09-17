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
            // TODO  暂时为user id 后期改为邀请码
            // 邀请码以为空，存在就必须存在在users表中的id
            'parent_id'         => [
                'nullable',
                'exists:users,id'
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
