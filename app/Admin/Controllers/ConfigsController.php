<?php

namespace App\Admin\Controllers;

use App\Handlers\ImageUploadHandler;
use App\Models\Config;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Form\Field;
use function GuzzleHttp\Promise\all;

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

    public function wechat_rate(Content $content)
    {
        $id = Config::query()->where('name','wechat_rate')->value('id');
        $form = new Form(new Config());
        $form->rate('value','微信提现费率');
        $form->setAction('/admin/configs/'.$id);
        return $content
            ->header('微信提现费率')
            ->body($form->edit($id));
    }
    public function alipay_rate(Content $content)
    {
        $id = Config::query()->where('name','alipay_rate')->value('id');
        $form = new Form(new Config());
        $form->rate('value','支付宝提现费率');
        $form->setAction('/admin/configs/'.$id);
        return $content
            ->header('微信提现费率')
            ->body($form->edit($id));
    }

    public function bank_rate(Content $content)
    {
        $id = Config::query()->where('name','bank_rate')->value('id');
        $form = new Form(new Config());
        $form->rate('value','银行卡提现费率');
        $form->setAction('/admin/configs/'.$id);
        return $content
            ->header('银行卡提现费率')
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
        if($name == 'wechat_rate' || $name == 'alipay_rate' || $name == 'bank_rate'){
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
}
