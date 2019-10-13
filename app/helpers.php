<?php

/**
 * Created by PhpStorm.
 * User: leona
 * Date: 2019/9/12
 * Time: 23:12
 */

function site_config()
{
    $seconds = 60;
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