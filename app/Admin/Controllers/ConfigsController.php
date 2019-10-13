<?php

namespace App\Admin\Controllers;

use App\Handlers\ImageUploadHandler;
use App\Models\Config;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class ConfigsController extends Controller
{
    use HasResourceActions;
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '站点配置';

    /**
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('配置列表')
            ->body($this->grid());
    }
    /**
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        $form = new Form(new Config);
        $form->text('name', '标识');
        $form->text('title', '名称');
        return $content
            ->header('添加配置')
            ->body($form);
    }


    public function manager(Content $content)
    {
        $id = Config::query()->where('name','manager')->value('id');
        $form = new Form(new Config());
        $form->text('title', '名称');
        $form->editor('extra', '介绍');
        $form->setAction('/admin/configs/'.$id);
        return $content
            ->header('门店经理配置')
            ->body($form->edit($id));
    }

    public function player(Content $content)
    {
        $id = Config::query()->where('name','player')->value('id');
        $form = new Form(new Config());
        $form->text('title', '名称');
        $form->editor('extra', '介绍');

        $form->setAction('/admin/configs/'.$id);
        return $content
            ->header('酱紫玩家配置')
            ->body($form->edit($id));
    }

    public function site(Content $content)
    {
        $id = Config::query()->where('name','site')->value('id');
        $form = new Form(new Config());
        $form->table('value','配置', function ($table) {
            $table->text('desc','描述');
            $table->text('key','标识');
            $table->text('value','值');
        });
        $form->setAction('/admin/configs/'.$id);
        return $content
            ->header('站点配置')
            ->body($form->edit($id));
    }

    public function wechat(Content $content)
    {
        $id = Config::query()->where('name','wechat')->value('id');
        $form = new Form(new Config());
        $form->image('image','客服二维码');
        $form->setAction('/admin/configs/'.$id);
        return $content
            ->header('客服微信号')
            ->body($form->edit($id));
    }


    public function bank_rate(Content $content)
    {
        $id = Config::query()->where('name','bank_rate')->value('id');
        $form = new Form(new Config());
        $form->rate('value','提现费率');
        $form->setAction('/admin/configs/'.$id);
        return $content
            ->header('提现费率')
            ->body($form->edit($id));
    }

    public function withdraw_amount(Content $content)
    {
        $id = Config::query()->where('name','withdraw_amount')->value('id');
        $form = new Form(new Config());
        $form->currency('value','每次提现最高额度')->symbol('￥');
        $form->setAction('/admin/configs/'.$id);
        return $content
            ->header('每次提现最高额度')
            ->body($form->edit($id));
    }

    public function withdraw_date(Content $content)
    {
        $id = Config::query()->where('name','withdraw_date')->value('id');
        $form = new Form(new Config());
        $form->text('value','每月提现日期');
        $form->setAction('/admin/configs/'.$id);
        return $content
            ->header('每月提现日期    ')
            ->body($form->edit($id));
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Config);
        $grid->column('title', '配置项目');
        $grid->column('do', '操作')->display(function (){
            return '<a href="/admin/configs/'.$this->name.'" >查看</a>';
        });
        $grid->disablePagination();
        $grid->disableFilter();
        $grid->disableExport();
        $grid->disableRowSelector();
        $grid->disableActions();
        $grid->disableColumnSelector();
        // 全部关闭
        $grid->disableActions();

        return $grid;
    }

    public function update($id)
    {

        $name = Config::query()->where('id' , $id)->value('name');
        if($name == 'bank_rate' || $name == 'withdraw_date' || $name == 'withdraw_amount'){
            Config::query()->where('id' , $id)->update(['value' => request()->value]);
        }

        if($name == 'manager' || $name == 'player'){
            Config::query()->where('id' , $id)->update(['extra' => request()->extra]);
        }

        if($name == 'wechat'){
            $uploader = new ImageUploadHandler();
            $result = $uploader->save(request()->image, 'site', $id);
            if ($result) {
                Config::query()->where('id' , $id)->update(['image' => $result['path']]);
            }
        }

        admin_toastr(trans('admin.save_succeeded'));
        return redirect('/admin/configs/'.$name);

    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Config);

        $form->text('name', __('Name'));
        $form->text('title', __('Title'));
        $form->textarea('value', __('Value'));
        $form->image('image','图片');
        $form->textarea('extra', __('Extra a'));

        return $form;
    }
}
