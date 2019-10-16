<?php

/**
 * Created by PhpStorm.
 * User: leona
 * Date: 2019/9/12
 * Time: 23:12
 */


function get_client_config()
{
    $seconds = 60;
    $result = cache()->remember('client_config', $seconds, function () {
        $site_configs = \DB::table('configs')->select('name', 'title', 'value')->get();
        return $site_configs->keyBy('name');
    });
    return $result;
}


function update_client_config()
{
    $site_configs = \DB::table('configs')->select('name', 'title', 'value')->get();
    return cache()->put('client_config',$site_configs->keyBy('name'));
}

function destroy_client_config()
{
    return cache()->forget('client_config');
}

