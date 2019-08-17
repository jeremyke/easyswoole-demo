<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/5/28
 * Time: 下午6:33
 */

namespace EasySwoole\EasySwoole;

use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\Component\Di;
use App\Lib\Redis\Redis;
use EasySwoole\Component\Pool\PoolManager;
use EasySwoole\Utility\File;
use App\Lib\Process\ConsumerTest;
use EasySwoole\EasySwoole\Crontab\Crontab;
use App\Crontab\TestTask;
use EasySwoole\Component\Timer;
use EasySwoole\FastCache\Cache;
use App\Model\Es\EsClient;

class EasySwooleEvent implements Event
{

    public static function initialize()
    {
        // TODO: Implement initialize() method.
        date_default_timezone_set('Asia/Shanghai');
        //获得原先的config配置项,加载到新的配置项中
        self::loadConf(EASYSWOOLE_ROOT . '/Config');
    }

    //加载配置文件
    public static function loadConf($ConfPath)
    {
        $Conf  = Config::getInstance();
        $files = File::scanDirectory($ConfPath);
        foreach ($files['files'] as $file) {
            $data = require_once $file;
            $Conf->setConf(strtolower(basename($file, '.php')), (array)$data);
        }
    }


    public static function mainServerCreate(EventRegister $register)
    {
        // TODO: Implement mainServerCreate() method.
        //mysql
        Di::getInstance()->set('MYSQL',\MysqliDb::class,Array (
                'host' => '127.0.0.1',
                'username' => 'root',
                'password' => 'smile520.0',
                'db'=> 'easyswoole',
                'port' => 3306,
                'charset' => 'utf8')
        );
        //redis
        Di::getInstance()->set('REDIS',Redis::getInstance());
        //消息队列，消费
        /*$allNum = 3;
        for ($i = 0 ;$i < $allNum;$i++){
            ServerManager::getInstance()->getSwooleServer()->addProcess((new ConsumerTest("consumer_{$i}"))->getProcess());
        }*/
        //crontab任务计划
        //Crontab::getInstance()->addTask(TestTask::setRule("*/1 * * * *"));
        //swoole原生定时器timer
        $register->add(EventRegister::onWorkerStart, function (\swoole_server $server, $workerId) {
            if ($workerId == 0) {
                Timer::getInstance()->loop(10 * 1000, function () {
                    $obj = new \App\Lib\Cache\Video();
                    $obj->setIndexVideo();
                });
            }
        });
        //使用fast-cache
        Cache::getInstance()->setTempDir(EASYSWOOLE_TEMP_DIR)->attachToServer(ServerManager::getInstance()->getSwooleServer());
        //注入elasticsearch
        Di::getInstance()->set('ES',EsClient::getInstance());
    }

    public static function onRequest(Request $request, Response $response): bool
    {
        // TODO: Implement onRequest() method.
        return true;
    }

    public static function afterRequest(Request $request, Response $response): void
    {
        // TODO: Implement afterAction() method.
    }
}