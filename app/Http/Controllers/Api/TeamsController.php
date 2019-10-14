<?php

namespace App\Http\Controllers\Api;

use App\Models\InviteCode;
use App\Models\Order;
use App\Models\OrderCommissionLog;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;

class TeamsController extends Controller
{
    //
    public function index()
    {
        $user = $this->user();

        if($user->identity == User::USER_IDENTITY_STORE ||
            $user->identity == User::USER_IDENTITY_DEPARTMENT ||
            $user->identity == User::USER_IDENTITY_DIRECTOR ||
            $user->identity == User::USER_IDENTITY_PLAYER){

            // 团队
            $teams = Team::query()->select()
                ->where('top_id',$user->id)
                ->leftJoin('users','player_id','id')->get()->keyBy('player_id');


            // 团队订单
            $orders = Order::query()->whereIn('user_id',$teams->pluck('player_id'))->get()->keyBy('id');
            $orders->transform(function ($item) use ($teams){
                $item['user_name'] = $teams[$item->user_id]['name'];
                return $item;
            });

            // 团队奖励
            $commissions = OrderCommissionLog::query()->where([
                ['commission_user_id',$user->id],
            ])->get();
            $commissions->transform(function ($item) use ($orders){
                $item['no'] = $orders[$item->order_id]['no'];
                return $item;
            });

            // 二维码
            $invite_code = InviteCode::query()->where([['user_id',$user->id],['type','team']])->first();
            if(!$invite_code){
                $invite_code = new InviteCode(['type' => 'team']);
                $invite_code->user()->associate($user);
                $invite_code->save();
            }

            $data = [
                'teams'            => $teams->groupBy('role')->toArray(),
                'orders'           => $orders->groupBy('type')->toArray(),
                'commissions'      => $commissions->groupBy('type')->toArray(),
                'invite_code'      => $invite_code
            ];

            return $this->response->array($data)->setStatusCode(200);
        }

        return $this->response->errorNotFound();
    }
}
