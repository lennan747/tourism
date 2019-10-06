<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Order;
use Illuminate\Support\Arr;

class Upgrade implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Order $order, $delay)
    {
        //
        $this->order = $order;
        // 设置延迟的时间，delay() 方法的参数代表多少秒之后执行
        $this->delay($delay);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        // 判断对应的订单是否已经被支付
        // 如果已经支付则不需要关闭订单，直接退出
        if ($this->order->paid_at) {
            return;
        }

        // 获取所属所有团队队长
        $teams = \DB::table('users')->whereIn('id',$this->order->user()->tree)->orderBy('id','desc')->get();
        //            // 获取所属所有团队队长的团队
//            $top_teams = \DB::table('teams')->whereIn('top_id',$this->order->user()->tree)->get()->groupBy('top_id')->toArray();
        // 门店经理订单，订单用户一定为普通用户， 更新自己的角色 users,teams
        if($this->order->type == Order::ORDER_TYPE_STORE) {
            \DB::table('users')->where('id', $this->order->user_id)->update(['identity' => User::USER_IDENTITY_STORE]);
            \DB::table('teams')->where('player_id', $this->order->user_id)->update(['role' => User::USER_IDENTITY_STORE]);
            foreach ($teams as $team) {
                // 直接上一级 门店经理 1000 m >= 5 sj
                if ($team->id == $this->order->user()->parent_id) {
                    $m = 0;
                    // 获取上一级的所有
                    if ($m >= 5) {  // 升级为部门经理
                        \DB::table('users')->where('id', $team->id)->update(['identity' => User::USER_IDENTITY_DEPARTMENT]);
                        \DB::table('teams')->where('player_id', $team->id)->update(['role' => User::USER_IDENTITY_DEPARTMENT]);
                    }
                }

            }
        }

        // 酱紫玩家订单
        if($this->order->type == Order::ORDER_TYPE_PLAYER){

        }

        // 旅游订单
        if($this->order->type == Order::ORDER_TYPE_TOURISM){

        }

    }
}
