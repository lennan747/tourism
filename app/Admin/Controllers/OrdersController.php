<?php

namespace App\Admin\Controllers;

use App\Exceptions\InvalidRequestException;
use App\Models\Config;
use App\Models\Order;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\MessageBag;
use App\Jobs\Upgrade;

class OrdersController extends Controller
{
    use HasResourceActions;
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '订单';

    /**
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('订单列表')
            ->body($this->grid());
    }

    public function show(Order $order, Content $content)
    {
        return $content
            ->header('查看订单')
            // body 方法可以接受 Laravel 的视图作为参数
            ->body(view('admin.orders.show', ['order' => $order]));
    }


    //  审核未支付订单
    public function review(Order $order, Request $request)
    {

        // 验证
        $data = $this->validate($request, [
            'payment_method' => ['required'],
            'remark'         => ['nullable'],
            'review'         => ['required']
        ], [], [
            'payment_method' => '支付方式',
            'remark' => '订单备注',
            'review' => '是否通过',
        ]);
        // 判断当前订单发货状态是否为未发货
        if ($order->pay_status !== Order::PAY_STATUS_UNPAID) {
            throw new InvalidRequestException('该订单已支付');
        }

        // 将订单发货状态改为已发货，并存入物流信息

        $order->update([
            'pay_status' => $data['review'] == 'true' ? Order::PAY_STATUS_PAID : Order::PAY_STATUS_UNPAID,
            'payment_method' => $data['review'] == 'true' ? $data['payment_method'] : null,
            'remark' => $data['remark'],
            'paid_at' => $data['review'] == 'true' ? Carbon::now() : null,
            'closed' => true
        ]);

        // 审核通过进行分成任务
        if($data['review'] == 'true'){
            $this->dispatch(new Upgrade($order, config('app.order_ttl')));
        }
        // 返回上一页
        $success = new MessageBag([
            'title' => '订单审核',
            'message' => '订单' . $order->no . '通过',
        ]);

        $error = new MessageBag([
            'title' => '订单审核',
            'message' => '订单' . $order->no . '不通过',
        ]);
        return back()->with(compact($data['review'] == 'true' ? 'success': 'error'));
    }


    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Order);

        $grid->column('no', '订单号');
        $grid->column('user.phone', '下单用户手机号');
        $grid->column('total_amount', '订单金额');
        $grid->column('pay_status', '支付状态')->display(function () {
            return Order::$payStatusMap[$this->pay_status];
        });
        $grid->column('paid_at', '支付时间');
        $grid->column('payment_method', '支付方式');
        $grid->column('closed', '是否关闭')->display(function () {
            return $this->closed ? '已关闭' : '进行中';
        });
        $grid->column('type', '订单类型')->display(function () {
            return Order::$orderTypeMap[$this->type];
        });
        $grid->column('created_at', '下单时间');

        // 禁用创建按钮，后台不需要创建订单
        $grid->disableCreateButton();
        $grid->actions(function ($actions) {
            // 禁用删除和编辑按钮
            $actions->disableDelete();
            $actions->disableEdit();
        });
        $grid->tools(function ($tools) {
            // 禁用批量删除按钮
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
        });

        return $grid;
    }
}
