<?php

namespace App\Admin\Controllers;

use App\Models\OrderCommissionLog;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class OrderCommissionLogsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '订单分成日志';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new OrderCommissionLog);

        $grid->column('id', __('ID'));
        $grid->column('order.no', '所属订单');
        $grid->column('commission_user.name', '分成用户');
        $grid->column('money', '分成金额');
        $grid->column('type', '分成类型')->display(function (){
            $type = [
                'tourism_d' => '直接购买旅游产品',
                'tourism_t' => '团队购买旅游产品',
                'store_d'   => '直接购买门店经理',
                'store_t'   => '团队购买门店经理',
                'player'    => '购买酱紫玩家'
            ];

            return $type[$this->type];
        });
        $grid->column('desc', '描述');
        $grid->column('updated_at', '记录时间');

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
        $show = new Show(OrderCommissionLog::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('order_id', __('Order id'));
        $show->field('commission_user_id', __('Commission user id'));
        $show->field('money', __('Money'));
        $show->field('type', __('Type'));
        $show->field('desc', __('Desc'));
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
        $form = new Form(new OrderCommissionLog);

        $form->number('order_id', __('Order id'));
        $form->number('commission_user_id', __('Commission user id'));
        $form->decimal('money', __('Money'));
        $form->text('type', __('Type'));
        $form->textarea('desc', __('Desc'));

        return $form;
    }
}
