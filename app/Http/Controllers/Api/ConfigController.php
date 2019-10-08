<?php

namespace App\Http\Controllers\Api;

use App\Models\Config;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class ConfigController extends Controller
{
    //
    public function index()
    {
        $seconds = '60';
        $result = Cache::remember('site_config', $seconds, function () {
            $site_configs = Config::all(['name','title','value','image','extra'])->toArray();
            $configs = [];
            foreach ($site_configs as $item){
                $configs[$item['name']] = Arr::except($item, ['name']);;
            }
            return $configs;
        });
        if(true){
            return $this->response->array($result)->setStatusCode(200);
        }
        return $this->response->array([])->setStatusCode(503);
    }
}
