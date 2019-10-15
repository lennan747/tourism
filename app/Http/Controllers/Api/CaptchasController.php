<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Gregwar\Captcha\CaptchaBuilder;
use App\Http\Requests\Api\CaptchaRequest;

class CaptchasController extends Controller
{
    // 过期时间
    public $expiredTime = 3;

    // 注册手机短信验证码
    public function register(CaptchaRequest $request)
    {
        $key = 'captcha-register'.str_random(15);
        $result = $this->store($key,$request->phone);
        return $this->response->array($result)->setStatusCode(201);
    }

    // 重置密码短信验证码
    public function reset_password(CaptchaRequest $request)
    {
        $key = 'captcha-reset_password'.str_random(15);
        $result = $this->store($key,$request->phone);
        return $this->response->array($result)->setStatusCode(201);
    }


    // 下单验证码
    public function order()
    {
        $key = 'captcha-order'.str_random(15);
        $result = $this->store($key);
        return $this->response->array($result)->setStatusCode(201);
    }

    // 创建图片验证码
    public function store($key,$phone = null)
    {
        $captchaBuilder = new CaptchaBuilder();
        $captcha = $captchaBuilder->build();
        $expiredAt = now()->addMinutes($this->expiredTime);
        $cache_data = $phone == null ? ['code' => $captcha->getPhrase()] : ['phone' => $phone, 'code' => $captcha->getPhrase()];
        \Cache::put($key, $cache_data, $expiredAt);
        $result = [
            'captcha_key'           => $key,
            'expired_at'            => $expiredAt->toDateTimeString(),
            'captcha_image_content' => $captcha->inline()
        ];
        return $result;
    }
}
