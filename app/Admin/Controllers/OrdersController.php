<?php

namespace App\Admin\Controllers;

use App\Models\Order;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

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

        //$grid->column('id', __('Id'));
        $grid->column('no', '订单号');
        $grid->column('user_id', '下单用户');
        //$grid->column('address', __('Address'));
        $grid->column('total_amount','订单金额');
        //$grid->column('remark', __('Remark'));
        $grid->column('pay_status', '支付状态');
        $grid->column('paid_at', '支付时间');
        $grid->column('payment_method', '支付方式');
        //$grid->column('payment_no', __('Payment no'));
        //$grid->column('refund_status', __('Refund status'));
        //$grid->column('refund_no', __('Refund no'));
        $grid->column('closed', '是否关闭');
        //$grid->column('reviewed', __('Reviewed'));
        //$grid->column('ship_status', __('Ship status'));
        //$grid->column('ship_data', __('Ship data'));
        $grid->column('type', '订单类型');
        //$grid->column('extra', __('Extra'));
        $grid->column('created_at', '下单时间');
        //$grid->column('updated_at', __('Updated at'));

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
        $show = new Show(Order::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('no', __('No'));
        $show->field('user_id', __('User id'));
        $show->field('address', __('Address'));
        $show->field('total_amount', __('Total amount'));
        $show->field('remark', __('Remark'));
        $show->field('pay_status', __('Pay status'));
        $show->field('paid_at', __('Paid at'));
        $show->field('payment_method', __('Payment method'));
        $show->field('payment_no', __('Payment no'));
        $show->field('refund_status', __('Refund status'));
        $show->field('refund_no', __('Refund no'));
        $show->field('closed', __('Closed'));
        $show->field('reviewed', __('Reviewed'));
        $show->field('ship_status', __('Ship status'));
        $show->field('ship_data', __('Ship data'));
        $show->field('type', __('Type'));
        $show->field('extra', __('Extra'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
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
}
