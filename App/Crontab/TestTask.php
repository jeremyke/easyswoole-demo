<?php
/**
 * Description:
 * User: Jeremy.Ke
 * Time: 2019/8/1 17:32
 */
namespace App\Crontab;

use EasySwoole\EasySwoole\Crontab\AbstractCronTask;
use App\Lib\Cache\Video as videoCache;

class TestTask extends AbstractCronTask
{
    public static $rules= "*/1 * * * *";
    public static function getRule(): string
    {
        return self::$rules;
    }
    public static function setRule($rules)
    {
        self::$rules = $rules;
        return self::class;
    }

    public static function getTaskName(): string
    {
        // TODO: Implement getTaskName() method.
        // 定时任务名称
        return 'taskOne';
    }

    static function run(\swoole_server $server, int $taskId, int $fromWorkerId,$flags=null)
    {
        $videoCache = new videoCache();
        $videoCache->setIndexVideo();
    }
}