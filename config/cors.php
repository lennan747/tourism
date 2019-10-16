<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laravel CORS
    |--------------------------------------------------------------------------
    |
    | allowedOrigins, allowedHeaders and allowedMethods can be set to array('*')
    | to accept any value.
    |
    */
    // 是否携带 Cookie
    'supportsCredentials' => false,
    //允许的域名
    'allowedOrigins' => ['*'],
    // 通过正则匹配允许的域名
    'allowedOriginsPatterns' => [],
    // 允许的 Header
    'allowedHeaders' => ['*'],
    // 允许的 HTTP 方法
    'allowedMethods' => ['*'],
    // 除了 6 个基本的头字段，额外允许的字段
    'exposedHeaders' => [],
    // 预检请求的有效期
    'maxAge' => 0,

];
