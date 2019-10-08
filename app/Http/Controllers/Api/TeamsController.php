<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;

class TeamsController extends Controller
{
    //
    public function index()
    {
        // 获取当前用户的所有队员
        $user = $this->user();

        // 酱紫玩家
        if($user->identity == User::USER_IDENTITY_PLAYER){
            //$result = Team::query()->where('top_id');
            return $this->response->array([])->setStatusCode(200);
        }

        // 门店经理
        if($user->identity == User::USER_IDENTITY_STORE ||
            $user->identity == User::USER_IDENTITY_DEPARTMENT ||
            $user->identity == User::USER_IDENTITY_DIRECTOR){

            $teams = Team::query()->select()
                ->where('top_id',$user->id)
                ->leftJoin('users','player_id','id')->get();
            // 获取
//            $number = $result->count();
//            // 团队奖励
//            $reward =
            // 收客奖励


            $orders = Order::query()->whereIn('user_id',$teams->pluck('user_id'))->get();
            $data = [
                'teams'  => $teams->groupBy('role')->toArray(),
                'orders' => $orders,
            ];
            return $this->response->array($data)->setStatusCode(200);
        }

        return $this->response->errorNotFound();
    }
}