<?php

namespace App\Admin\Controllers;

use App\Handlers\ImageUploadHandler;
use App\Models\Product;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Http\Request;

class ProductsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '商品';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Product);

        $grid->column('id', __('ID'))->sortable();
        $grid->column('type', '商品类型')->display(function ($value) {
            return $value == 'tourism' ? '旅游商品' : '普通商品';
        });
        $grid->column('title', '商品名称');
        $grid->column('on_sale', '已上架')->display(function ($value) {
            return $value ? '是' : '否';
        });
        $grid->column('rating', '评分');
        $grid->column('sold_count', '销量');
        $grid->column('review_count','评论数');
        $grid->column('price', '价格');
        //$grid->column('description', __('Description'));
        //$grid->column('index_image', __('Index image'));
        //$grid->column('image', __('Image'));
        //$grid->column('product_detail', '评分');
        //$grid->column('cost_detail', __('Cost detail'));
        //$grid->column('journey_detail', __('Journey detail'));
//        $grid->column('created_at', __('Created at'));
//        $grid->column('updated_at', __('Updated at'));
        $grid->actions(function ($actions) {
            $actions->disableView();
            $actions->disableDelete();
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
        $show = new Show(Product::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('type', __('Type'));
        $show->field('title', __('Title'));
        $show->field('description', __('Description'));
        $show->field('index_image', __('Index image'));
        $show->field('image', __('Image'));
        $show->field('on_sale', __('On sale'));
        $show->field('rating', __('Rating'));
        $show->field('sold_count', __('Sold count'));
        $show->field('review_count', __('Review count'));
        $show->field('price', __('Price'));
        $show->field('product_detail', __('Product detail'));
        $show->field('cost_detail', __('Cost detail'));
        $show->field('journey_detail', __('Journey detail'));
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
        $form = new Form(new Product);

        $form->column(1/2, function ($form) {
            //$form->text('type', '类型')->rules('required');
            $form->select('type', '类型')->options(['tourism' => '旅游商品'])->rules('required');
            $form->text('title','商品标题')->rules('required');
            $form->text('description', '商品描述')->rules('required');
            $form->radio('on_sale','上架')->options(['1' => '是', '0'=> '否'])->default('0');
            $form->radio('on_recommend','推荐')->options(['1' => '是', '0'=> '否'])->default('0');
            $form->image('index_image', '首页缩略图')->rules('nullable|image')->removable()->uniqueName()->move('/images/products/'.date("Ym/d", time()))->rules('required');
            $form->multipleImage('image', '商品图片')->rules('required|image')->removable()->uniqueName()->move('/images/products/'.date("Ym/d", time()))->sortable();
            $form->editor('product_detail', '商品详情');
            $form->editor('cost_detail', '费用详情');
            $form->editor('journey_detail','行程计划');
        });
        // style="background-image:url(http://tourism.cam/uploads/images/products/201910/05/1.jpg);">
        $form->column(1/2, function ($form) {
            $form->hasMany('skus', 'SKU 列表', function (Form\NestedForm $form) {
                $form->text('title', 'SKU 名称')->rules('required');
                $form->text('description', 'SKU 描述')->rules('required');
                $form->decimal('price', '单价')->rules('required|numeric|min:0.01');
                $form->decimal('profit', '利润')->rules('required|numeric|min:0.01');
                $form->text('stock', '剩余库存')->rules('required|integer|min:0');
            });
        });


        // 定义事件回调，当模型即将保存时会触发这个回调
        $form->saving(function (Form $form) {
            $form->model()->price = collect($form->input('skus'))->where(Form::REMOVE_FLAG_NAME, 0)->min('price') ?: 0;
        });
        return $form;
    }


    public function upload(Request $request,ImageUploadHandler $uploader)
    {
        $urls = [];

        foreach ($request->file() as $file) {
            $result = $uploader->save($file, 'products', 'p');
            if ($result) {
                $urls[] = $result['path'];
            }
        }

        return response()->json([
            "errno" => 0,
            "data"  => $urls,
        ]);
    }
}
