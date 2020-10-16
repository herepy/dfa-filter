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
    protected $filter;

    public function __construct()
    {
        $this->server = new \Swoole\Http\Server($this->config['host'],$this->config['port']);
        $this->init();
    }

    private function init()
    {
        //载入配置
        $this->config = require_once 'config.php';

        //初始化服务器
        $this->server->set($this->config['swoole']);
        $this->server->on('request',[$this,'request']);

        //初始化过滤器
        $this->filter = Filter::build();
        $this->filter->importSensitiveFile($this->config['filter']['file'],$this->config['filter']['delimiter']);
        $this->filter->addDisturbance($this->config['filter']['delimiter']);
    }

    public function request($request,$response)
    {
        if ($request->server['request_method'] != 'POST') {
            return;
        }

        $action = $request->post['action'];
        $data = $request->post['data'];
        try {
            $result = $this->doAction($action,$data);
        } catch (\Exception $exception) {
            $result = 'error:'.$exception->getMessage();
        }

        $response->end($result);
    }

    protected function doAction($action,$data)
    {
        $method = new \ReflectionMethod(Filter::class,$action);
        $params = $method->getParameters();

        $vars = [];
        foreach ($params as $p) {
            if (!isset($data[$p->getName()])) {
                throw new \Exception('miss param '.$p->getName());
            }

            $vars[] = $data[$p->getName()];
        }

        return $method->invokeArgs($this->filter,$vars);
    }

    public function run()
    {
        $this->server->start();
    }

}