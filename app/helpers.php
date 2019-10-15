<?php

/**
 * Created by PhpStorm.
 * User: leona
 * Date: 2019/9/12
 * Time: 23:12
 */

/**
 * 获取系统配置
 * @return mixed
 */
function site_config()
{
    $seconds = 3600;
    $result = \Cache::remember('site_config', $seconds, function () {
        $site_configs = \DB::table('configs')->select('name', 'title', 'value', 'image', 'extra')->get();
        $configs = [];
        foreach ($site_configs as $item) {
            $configs[$item->name] = [
                'title' => $item->title,
                'value' => $item->value,
                'image' => $item->image,
                'extra' => $item->extra,
            ];
        }
        return $configs;
    });
    return $result;
}

/**
 * 更新系统配置
 * @return mixed
 */
function update_site_config()
{
    $site_configs = \DB::table('configs')->select('name', 'title', 'value', 'image', 'extra')->get();
    $configs = [];
    foreach ($site_configs as $item) {
        $configs[$item->name] = [
            'title' => $item->title,
            'value' => $item->value,
            'image' => $item->image,
            'extra' => $item->extra,
        ];
    }
    return \Cache::put('site_config',$configs);
}

