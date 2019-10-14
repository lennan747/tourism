<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\WithdrawRequest;
use App\Models\Withdraw;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WithdrawsController extends Controller
{
    //

    public function store(WithdrawRequest $request)
    {
        // 获取当前系统配置
        $site_app = site_config();
        $user = $this->user();
        $application_date = Carbon::today()->toDateString();
        // 提现额度超系统额度
        if($site_app['withdraw_date']['value'] != date('j')) {
            return $this->response->errorUnauthorized('每月'.$site_app['withdraw_date'].'开启提现');
        }
        // 提现额度超用户余额
        if($request->application_amount > $site_app['withdraw_amount'] ||
            $request->application_amount > $user->money){
            return $this->response->errorUnauthorized('超过提现额度');
        }

        //  提现到银行卡
        $bank_card = $user->bank_card()->where('id',$request->bank_card_id)->first();

        // 判断当前用户当月是否有提现记录，如果存在则不允许提现
        if(Withdraw::query()->where([['user_id',$user->id],['application_date', $application_date]])->exists()){
            return $this->response->errorUnauthorized('当日已提现');
        }
        // 创建提现记录
        $withdraw = new Withdraw([
            'application_amount' => $request->application_amount,
            'application_date'   => $application_date,
            'bank_card'          => $bank_card,
            'status'             => Withdraw::REVIEW_STATUS_APPLICATION,
        ]);
        $withdraw->user()->associate($user);
        $withdraw->save();

        return $this->response->array($withdraw)->setStatusCode(201);
    }

    public function index()
    {
        $withdraws = $this->user()->withdraw()->get();
        return $this->response->array($withdraws->toArray())->setStatusCode(200);
    }
}
