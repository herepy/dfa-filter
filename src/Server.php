<?php
/**
 * Created by PhpStorm.
 * User: pengyu
 * Date: 2020/10/16
 * Time: 11:36
 */


namespace Pengyu\DfaFilter;


class Server
{
    protected $server;
    protected $config;

    public function __construct()
    {
        $this->server = new \Swoole\Http\Server($this->config['host'],$this->config['port']);
        $this->init();
    }

    private function init()
    {
        $this->config = require_once 'config.php';
        $this->server->set($this->config['swoole']);
        $this->server->on('request',[$this,'request']);
    }

    public function request($request,$response)
    {
        if ($request->server['request_method'] != 'POST') {
            return;
        }

        $action = $request->post['action'];
        $data = $request->post['data'];
        //todo 执行对应操作
    }

}