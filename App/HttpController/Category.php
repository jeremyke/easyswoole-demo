<?php
/**
 * Description:
 * User: Jeremy.Ke
 * Time: 2019/1/18 23:11
 */
namespace App\HttpController;

use App\Lib\Redis\Redis;
use EasySwoole\Core\Component\Di;

class Category extends Base
{
    /**
     * 首页
     * @author : evalor <master@evalor.cn>
     */
    public function index()
    {
        $this->response()->write('welcome to easyswoole');
    }

    public function setRedis()
    {
        Di::getInstance()->get('REDIS')->set('klp','123',50);
    }
    public function getRedis()
    {
        $res = Di::getInstance()->get('REDIS')->get('klp');
        $this->writeJson('200','请求成功',$res);
        //Redis::getInstance();
        //$redis->set('klp','12345');
    }
    public function yaconf()
    {
        $res = \Yaconf::get('redis');
        return $this->writeJson('200','请求成功',$res);
    }
    //消息队列生产者
    public function pub()
    {
        $param = $this->request()->getRequestParam();
        Di::getInstance()->get("REDIS")->rPush('my_list',$param['f']);
    }
}