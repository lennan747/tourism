<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\AuthorizationRequest;
use Illuminate\Http\Request;

class AuthorizationsController extends Controller
{
    //
    public function store(AuthorizationRequest $request)
    {
        $credentials['phone']    = $request->phone;
        $credentials['password'] = $request->password;

        // 获取登录token
        if(!$token = \Auth::guard('api')->attempt($credentials)){
            return $this->response->errorUnauthorized('用户名或密码错误');
        }

        return $this->respondWithToken($token);
    }

    public function update()
    {
        $token = \Auth::guard('api')->refresh();
        return $this->respondWithToken($token);
    }

    public function destroy()
    {
        \Auth::guard('api')->logout();
        return $this->response->noContent();
    }

    protected function respondWithToken($token)
    {
        return $this->response->array([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => \Auth::guard('api')->factory()->getTTL() * 60
        ]);
    }
}
