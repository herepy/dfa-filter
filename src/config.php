<?php
/**
 * Created by PhpStorm.
 * User: pengyu
 * Date: 2020/10/16
 * Time: 11:38
 */

return [
    'host'      =>  '0.0.0.0',
    'port'      =>  9501,
    'swoole'    =>  [
        'reactor_num'   => 1,     // reactor thread num
        'worker_num'    => 1,     // worker process num
        'backlog'       => 128,   // listen backlog
        'max_request'   => 200,
        'dispatch_mode' => 1,
    ],
    'filter'    =>  [
        'file'          =>  './src/test.txt',   // 敏感词文件
        'delimiter'     =>  PHP_EOL,  // 文件内容分隔符
        'disturbance'   =>  []    // 干扰因子
    ]
];