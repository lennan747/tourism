<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;

class HomeController extends Controller
{
    public function index(Content $content)
    {
        // 统计今日订单
        //Order::query()->where('')
        // 统计今日注册用户

        // 统计待审订单


        return $content
            ->title('Dashboard')
            ->description('首页')
            ->body(view('admin.home.index'));
    }
}
