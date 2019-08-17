<?php
namespace App\Lib\Redis;

use EasySwoole\Component\Singleton;
use EasySwoole\EasySwoole\Config;

class Redis
{
    use Singleton;
    public $redis = "";

    private function __construct() {
        try {
            if(!extension_loaded('redis')){
                throw new \Exception('redis扩展异常',400);
            }
            $this->redis = new \Redis();
            //$redis_conf = Config::getInstance()->getConf('redis');
            $redis_conf = \Yaconf::get('redis');
            $result = $this->redis->connect($redis_conf['host'],$redis_conf['port'],$redis_conf['time_out']);
            if($result===false){
                throw new \Exception("redis连接失败",500);
            }
        } catch(\Exception $e) {
            if(!empty($e->getCode())){
                throw new \Exception($e->getMessage());
            }else{
                throw new \Exception("redis服务异常");
            }
        }
    }

    public function get($key) {
        if(empty($key)) {
            return '';
        }
        return $this->redis->get($key);
    }

    public function set($key, $value, $time = 0) {
        if(empty($key)) {
            return '';
        }
        if(is_array($value)) {
            $value = json_encode($value);
        }
        if(!$time) {
            return $this->redis->set($key, $value);
        }
        return $this->redis->setex($key, $time, $value);
    }

    public function lPop($key) {
        if(empty($key)) {
            return '';
        }

        return $this->redis->lPop($key);
    }

    public function rPush($key, $value) {
        if(empty($key)) {
            return '';
        }

        return $this->redis->rPush($key, $value);
    }

    public function zincrby($key, $number, $member) {
        if(empty($key) || empty($member)) {
            return false;
        }

        return $this->redis->zincrby($key, $number, $member);
    }
    public function zrevrange($key,$start,$stop,$type)
    {
        if(empty($key)){
            return false;
        }
        return $this->redis->zrevrange($key,$start,$stop,$type);
    }

    /**
     * 当类中不存在该方法时候，直接调用call 实现调用底层redis相关的方法
     * ...$arguments 可变长度的参数
     */
    public function __call($name, $arguments) {

        ///var_dump(...$arguments);
        return $this->redis->$name(...$arguments);
    }

}