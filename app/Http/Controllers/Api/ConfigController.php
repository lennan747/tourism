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
        $config = site_config();
        return $this->response->array($config)->setStatusCode(200);
    }
}
