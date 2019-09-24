<?php

namespace App\Admin\Controllers;

use App\Exceptions\InvalidRequestException;
use App\Models\Order;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class OrdersController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '订单';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Order);

        $grid->column('no', '订单号');
        $grid->column('user.phone','下单用户手机号');
        $grid->column('total_amount','订单金额');
        $grid->column('pay_status', '支付状态')->display(function (){
            return Order::$payStatusMap[$this->pay_status];
        });
        $grid->column('paid_at', '支付时间');
        $grid->column('payment_method', '支付方式');
        $grid->column('closed', '是否关闭');
        $grid->column('type', '订单类型')->display(function (){
            return Order::$orderTypeMap[$this->type];
        });
        $grid->column('created_at', '下单时间');
        //$grid->column('remark', __('Remark'));
        //$grid->column('address', __('Address'));
        //$grid->column('user_id', '下单用户');
        //$grid->column('id', __('Id'));
        //$grid->column('payment_no', __('Payment no'));
        //$grid->column('refund_status', __('Refund status'));
        //$grid->column('refund_no', __('Refund no'));
        //$grid->column('reviewed', __('Reviewed'));
        //$grid->column('ship_status', __('Ship status'));
        //$grid->column('ship_data', __('Ship data'));
        //$grid->column('extra', __('Extra'));
        //$grid->column('updated_at', __('Updated at'));

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

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $order = Order::findOrFail($id);
        return view('admin.orders.show', ['order' => $order]);
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Order);

        $form->text('no', __('No'));
        $form->number('user_id', __('User id'));
        $form->textarea('address', __('Address'));
        $form->decimal('total_amount', __('Total amount'));
        $form->textarea('remark', __('Remark'));
        $form->text('pay_status', __('Pay status'));
        $form->datetime('paid_at', __('Paid at'))->default(date('Y-m-d H:i:s'));
        $form->text('payment_method', __('Payment method'));
        $form->text('payment_no', __('Payment no'));
        $form->text('refund_status', __('Refund status'))->default('pending');
        $form->text('refund_no', __('Refund no'));
        $form->switch('closed', __('Closed'));
        $form->switch('reviewed', __('Reviewed'));
        $form->text('ship_status', __('Ship status'))->default('pending');
        $form->textarea('ship_data', __('Ship data'));
        $form->text('type', __('Type'))->default('tourism');
        $form->textarea('extra', __('Extra'));

        return $form;
    }

    //  审核未支付订单
    public function review(Order $order, Request $request){

        // 验证
        $data = $this->validate($request, [
            'payment_method'  => ['required'],
            'remark'          => ['nullable'],
        ], [], [
            'payment_method' => '支付方式',
            'remark'         => '订单备注',
        ]);

        // 判断当前订单发货状态是否为未发货
        if ($order->pay_status !== Order::PAY_STATUS_UNPAID) {
            throw new InvalidRequestException('该订单已支付');
        }

        // 将订单发货状态改为已发货，并存入物流信息
        $order->update([
            'pay_status'     => Order::PAY_STATUS_PAID,
            'payment_method' => $data['payment_method'],
            'remark'         => $data['remark'],
            'paid_at'        => Carbon::now()
        ]);

        // 返回上一页
        return redirect()->back();
    }
}
