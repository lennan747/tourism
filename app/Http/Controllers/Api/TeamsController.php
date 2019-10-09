<?php

namespace App\Http\Controllers\Api;

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

            $teams = Team::query()->select()
                ->where('top_id',$user->id)
                ->leftJoin('users','player_id','id')->get();

            $orders = Order::query()
                ->whereIn('user_id',$teams->pluck('player_id'))->get();

            // 团队奖励
            $commissions = OrderCommissionLog::query()->where([
                ['commission_user_id',$user->id],
            ])->get();

            $data = [
                'teams'       => $teams->groupBy('role')->toArray(),
                'orders'      => $orders->groupBy('type')->toArray(),
                'commissions'  => $commissions->groupBy('type')->toArray()
            ];
            return $this->response->array($data)->setStatusCode(200);
        }

        return $this->response->errorNotFound();
    }
}
