<?php

namespace App\Admin\Controllers;

use App\Models\User;
use App\Models\Withdraw;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\MessageBag;

class WithdrawsController extends Controller
{
    use HasResourceActions;
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '提现列表';

    /**
     * Get content title.
     *
     * @return string
     */
    protected function title()
    {
        return $this->title;
    }

    /**
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('提现列表')
            ->body($this->grid());
    }


    public function edit(Withdraw $withdraw, Content $content)
    {
        $type = [
            'alipay' => '支付宝',
            'wechat' => '微信',
            'bank' => '银行卡'
        ];
        return $content
            ->title($this->title())
            ->description($this->description['edit'] ?? trans('admin.edit'))
            ->body(view('admin.withdraw.show', ['withdraw' => $withdraw, 'type' => $type]));
    }


    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Withdraw);

        ///$grid->column('id', __('Id'));
        $grid->column('user.name', '提现用户');
        $grid->column('application_amount', '申请提现金额');
        //$grid->column('application_date', '提现开放时间');
        $grid->column('transfer_amount', '实际到账金额');
        $grid->column('transfer_date', '转账时间');
        $grid->column('handling_fee', '费率')->display(function ($v){
            return $v ? $v.'%' : '';
        });
        //$grid->column('bank_card', __('Bank card'));
        //$grid->column('reason', __('Reason'));
        //$grid->column('created_at', __('Created at'));
        $grid->column('updated_at', '申请时间');
        $grid->column('status', '状态')->display(function ($v) {
            return Withdraw::$reviewStatusMap[$v];
        });
        return $grid;
    }


    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {

        $form = new Form(new Withdraw);

        $form->number('user_id', '提现用户');
        $form->decimal('application_amount', '申请提现金额');
        //$form->date('application_date', __('Application date'))->default(date('Y-m-d'));
        $form->decimal('transfer_amount', '实际到账金额');
        $form->datetime('transfer_date','转账时间')->default(date('Y-m-d H:i:s'));
        $form->decimal('handling_fee', '费率');
        $form->textarea('bank_card', '转账银行卡');
        $form->text('status','审核状态');
        $form->text('reason', '拒绝理由');

        // 在表单提交前调用
        $form->submitted(function (Form $form) {
            //...
            $user = User::query()->where('id',$form->model()->user_id)->first();
            if($form->model()->application_amount > $user->money){
                $error = new MessageBag([
                    'title'   => '审核错误',
                    'message' => '当前用户余额不足',
                ]);
                return back()->with(compact('error'));
            }
        });
        //保存前回调
        $form->saving(function (Form $form) {
            if($form->status == Withdraw::REVIEW_STATUS_BY){
                $form->handling_fee = site_config()['bank_rate']['value'];
            }
        });

        $form->saved(function ($form){
            if($form->status == Withdraw::REVIEW_STATUS_BY){
                $user = User::query()->where('id',$form->model()->user_id)->first();
                User::query()->where('id',$form->model()->user_id)->update(['money' => ($user->money - $form->model()->application_amount)]);
            }
        });
        return $form;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Withdraw::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('user.name', '提现用户');
        $show->field('application_amount', '申请提现金额');
        //$show->field('application_date', __('Application date'));
        $show->field('transfer_amount', '实际到账金额');
        $show->field('transfer_date', '转账时间');
        $show->field('handling_fee', '费率');
        $show->field('bank_card','转账银行卡');
        $show->field('status', '审核状态');
        $show->field('reason', '拒绝理由');
        //$show->field('created_at', __('Created at'));
        $show->field('updated_at', '申请时间');
        return $show;
    }

}
