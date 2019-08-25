<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/25
 * Time: 22:29
 */
namespace App\HttpController\Api;
use App\Lib\Pool\MysqlPool;
use EasySwoole\Component\Pool\PoolManager;
use App\Model\Pool\Mysql\Video as VideoPool;

class Pool extends Base {

    public function mysqldemo() {

        /*$config = \Yaconf::get("mysql");
        $db = PoolManager::getInstance()->getPool(MysqlPool::class)->getObj($config['POOL_TIME_OUT']);
        $result = ($db->get('video'));
        return $this->writeJson(200, 'OK', $result);*/

        $obj = new VideoPool();
        $result = $obj->getById(1);
        return $this->writeJson(200, 'OK', $result);
    }
}