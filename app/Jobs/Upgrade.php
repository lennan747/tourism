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

    protected $order;
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
        // 判断对应的订单是否已经被支付
        // 如果已经支付则不需要关闭订单，直接退出
//        if (!$this->order->paid_at) {
//            return;
//        }
        // 门店经理订单
        \DB::transaction(function() {
            $user = $this->order->user()->first();
            $tree = explode(',', $user->tree);
            unset($tree[0]);
            $tree = array_reverse($tree);
            unset($tree[0]);
            // 购买门店经理
            if ($this->order->type == Order::ORDER_TYPE_STORE) {
                // 不是普通用户不进行操作
                if($user->identity != User::USER_IDENTITY_ORDINARY){
                    return;
                }
                // 更新用户信息
                $user->identity = User::USER_IDENTITY_STORE;
                $user->save();
                // 如果没有上一级
                if($user->parent_id == 0){
                    return;
                }
                // 更新团队信息
                \DB::table('teams')->where('player_id', $user->id)->update(['role' => User::USER_IDENTITY_STORE]);

                // 更新团队
                foreach ($tree as $v) {
                    $top = \DB::table('users')->where('id',$v)->first();
                    // 直属上级
                    if ($v == $user->parent_id) {
                        // 上级如果是门店经理
                        if ($top->identity == User::USER_IDENTITY_STORE) {
                            $store_count = \DB::table('teams')->select(\DB::raw('count(*) as store_count'))
                                ->where([['top_id', $top->id], ['depth', 1], ['role', User::USER_IDENTITY_STORE]])
                                ->value('store_count');
                            // 如果上级满足升级条件
                            if ($store_count >= 5) {
                                $top->identity = User::USER_IDENTITY_DEPARTMENT;
                                // 更新团队
                                \DB::table('teams')->where('player_id', $top->id)->update(['role' => User::USER_IDENTITY_DEPARTMENT]);
                            }
                            // 奖励上级
                            $top->money = $top->money + 1000.00;
                            \DB::table('users')->where('id', $top->id)->update(['identity' => $top->identity, 'money' =>  $top->money]);
                            continue;
                        }
                        // 上级如果是部门经理
                        if ($top->identity == User::USER_IDENTITY_DEPARTMENT) {
                            // 团队会员人数
                            $count1 = \DB::table('teams')->select(\DB::raw('count(*) as count'))->where([['top_id', $top->id], ['role', '!=', User::USER_IDENTITY_ORDINARY]])->value('count');
                            // 团队部门经理人数
                            $count2 = \DB::table('teams')->select(\DB::raw('count(*) as count'))->where([['top_id', $top->id], ['role', User::USER_IDENTITY_DEPARTMENT]])->value('count');
                            // 如果上级满足升级条件
                            if ($count1 >= 30 && $count2 >= 5) {
                                $top->identity = User::USER_IDENTITY_DIRECTOR;
                                // 更新团队
                                \DB::table('teams')->where('player_id', $top->id)->update(['role' => User::USER_IDENTITY_DIRECTOR]);
                            }
                            // 奖励上级
                            $top->money = $top->money + 1000.00;
                            \DB::table('users')->where('id', $top->id)->update(['identity' => $top->identity, 'money' =>  $top->money]);
                            continue;
                        }

                        // 上级如果是运营总监
                        if ($top->identity == User::USER_IDENTITY_DIRECTOR) {
                            $top->money = $top->money + 1680.00;
                            \DB::table('users')->where('id', $top->id)->update(['identity' => $top->identity, 'money' =>  $top->money]);
                            continue;
                        }

                        // 如果上级是酱紫玩家
                        if($top->identity == User::USER_IDENTITY_PLAYER){
                            continue;
                        }

                        // 如果上级是普通玩家
                        if($top->identity == User::USER_IDENTITY_ORDINARY){
                            continue;
                        }
                    }

                    // 上级如果是门店经理
                    if($top->identity == User::USER_IDENTITY_STORE){
                        continue;
                    }

                    // 如果上级是酱紫玩家
                    if($top->identity == User::USER_IDENTITY_PLAYER){
                        continue;
                    }

                    // 上级如果是部门经理
                    if ($top->identity == User::USER_IDENTITY_DEPARTMENT) {
                        // 团队会员人数
                        $count1 = \DB::table('teams')->select(\DB::raw('count(*) as count'))->where([['top_id', $top->id], ['role', '!=', User::USER_IDENTITY_ORDINARY]])->value('count');
                        // 团队部门经理人数
                        $count2 = \DB::table('teams')->select(\DB::raw('count(*) as count'))->where([['top_id', $top->id], ['role', User::USER_IDENTITY_DEPARTMENT]])->value('count');
                        // 如果上级满足升级条件
                        if ($count1 >= 30 && $count2 >= 5) {
                            $top->identity = User::USER_IDENTITY_DIRECTOR;
                            // 更新团队
                            \DB::table('teams')->where('player_id', $top->id)->update(['role' => User::USER_IDENTITY_DIRECTOR]);
                        }
                        // 奖励上级
                        $top->money = $top->money + 200.00;
                        \DB::table('users')->where('id', $top->id)->update(['identity' => $top->identity, 'money' =>  $top->money]);
                        continue;
                    }

                    // 上级如果是运营总监
                    if ($top->identity == User::USER_IDENTITY_DIRECTOR) {
                        $top->money = $top->money + 480.00;
                        \DB::table('users')->where('id', $top->id)->update(['identity' => $top->identity, 'money' =>  $top->money]);
                        continue;
                    }

                }
            }
            // 购买酱紫玩家
            if($this->order->type == Order::ORDER_TYPE_PLAYER){
                // 如果没有上一级
                if($user->parent_id == 0){
                    return;
                }
                $top = \DB::table('users')->where('id',$user->parent_id)->first();
                // 直接找到上一级，给与600
                $top->money = $top->money + 600.00;
                \DB::table('users')->where('id', $top->id)->update(['money' =>  $top->money]);
            }
            // 购买普通商品
            if($this->order->type == Order::ORDER_TYPE_TOURISM){
                // 如果没有上一级
                if($user->parent_id == 0){
                    return;
                }
                foreach ($tree as $v) {
                    $top = \DB::table('users')->where('id',$v)->first();
                    // 直属上一级
                    if ($v == $user->parent_id) {
                        // 上级如果是门店经理
                        if ($top->identity == User::USER_IDENTITY_STORE) {
                            // 获取上级直属团队的旅游订单，业绩总和25W升级
                            $players = \DB::table('teams')->where([['top_id', $top->id], ['depth', 1],['role',User::USER_IDENTITY_ORDINARY]])->pluck('player_id');
                            $total_sales = \DB::table('orders')->select(\DB::raw('SUM(total_amount) as total_sales'))
                                ->whereIn('user_id',$players->all())
                                ->where([['pay_status',Order::PAY_STATUS_PAID],['type',Order::ORDER_TYPE_TOURISM]])
                                ->value('total_sales');

                            // 如果上级满足升级条件
                            if ($total_sales >= 250000.00) {
                                $top->identity = User::USER_IDENTITY_DEPARTMENT;
                                // 更新团队
                                \DB::table('teams')->where('player_id', $top->id)->update(['role' => User::USER_IDENTITY_DEPARTMENT]);
                            }
                            // 奖励上级 该商品的80%

                            $top->money = $top->money + $this->order->total_profit * 0.8;
                            \DB::table('users')->where('id', $top->id)->update(['identity' => $top->identity, 'money' =>  $top->money]);
                            continue;
                        }
                        // 上级如果是部门经理
                        if ($top->identity == User::USER_IDENTITY_DEPARTMENT) {
                            // 奖励上级
                            $top->money = $top->money + $this->order->total_profit * 0.8;
                            \DB::table('users')->where('id', $top->id)->update(['identity' => $top->identity, 'money' =>  $top->money]);
                            continue;
                        }

                        // 上级如果是运营总监
                        if ($top->identity == User::USER_IDENTITY_DIRECTOR) {
                            $top->money = $top->money + $this->order->total_profit * 0.8;
                            \DB::table('users')->where('id', $top->id)->update(['identity' => $top->identity, 'money' =>  $top->money]);
                            continue;
                        }

                        // 上级如果是酱紫玩家
                        if ($top->identity == User::USER_IDENTITY_PLAYER) {
                            $top->money = $top->money + $this->order->total_profit * 0.7;
                            \DB::table('users')->where('id', $top->id)->update(['identity' => $top->identity, 'money' =>  $top->money]);
                            continue;
                        }

                        // 如果上级是普通玩家
                        if($top->identity == User::USER_IDENTITY_ORDINARY){
                            continue;
                        }
                    }

                    // 上级如果是门店经理
                    if($top->identity == User::USER_IDENTITY_STORE){
                        continue;
                    }

                    // 上级如果是酱紫玩家
                    if ($top->identity == User::USER_IDENTITY_PLAYER) {
                        continue;
                    }

                    // 上级如果是部门经理
                    if ($top->identity == User::USER_IDENTITY_DEPARTMENT) {
                        // 奖励上级
                        $top->money = $top->money + $this->order->total_profit * 0.05;
                        \DB::table('users')->where('id', $top->id)->update(['identity' => $top->identity, 'money' =>  $top->money]);
                        continue;
                    }

                    // 上级如果是运营总监
                    if ($top->identity == User::USER_IDENTITY_DIRECTOR) {
                        $top->money = $top->money + $this->order->total_profit * 0.1;
                        \DB::table('users')->where('id', $top->id)->update(['identity' => $top->identity, 'money' =>  $top->money]);
                        continue;
                    }
                }
            }
        });
    }
}
