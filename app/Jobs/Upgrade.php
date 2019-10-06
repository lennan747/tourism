<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Order;

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

        // 门店经理订单，订单用户一定为普通用户
        if($this->order->type == Order::ORDER_TYPE_STORE){
            // 获取所属所有团队
            $teams = \DB::table('users')->whereIn('id',$this->order->user()->tree)->orderBy('id','desc')->get();
            // 获取所属所有团队队长的团队
            $top_teams = \DB::table('teams')->whereIn('top_id',$this->order->user()->tree)->get()->groupBy('top_id')->toArray();
            // 更新自己的角色
            \DB::table('users')->where('id',$this->order->user_id)->update(['identity' => User::USER_IDENTITY_STORE]);

            // 计算直属老大的
            foreach ($teams as $team)
            {
                // 直接上一级 门店经理
                if($team->id == $this->order->user()->parent_id){
                    // 门店经理 1000 m >= 5 sj
                    $top_teams[$team->id];
                }

            }
            // 更新直属团队
            // 门店经理，
            // 按顺序更新其他团队


            // 以顶级分组，深度升序，依次计算
            // 深度1 为直属团队
            $top_teams_order = $top_teams->groupBy('top_id')->toArray();
            // 深度1 为直属团队
            $top_teams_order[$this->order->user()->parent_id];
            // 计算直属老大的
            foreach ($teams as $team)
            {
                if($team->id == $this->order->user_id){
                    \DB::table('users')->where('id',$this->order->user_id)->update(['identity' => User::USER_IDENTITY_STORE]);
                    continue;
                }

                // 直接上一级 门店经理
                if($team->id == $this->order->user()->parent_id){
                    // 门店经理 1000 m >= 5 sj
                    //$top_teams
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
