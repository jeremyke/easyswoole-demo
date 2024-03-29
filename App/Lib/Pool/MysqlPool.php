<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/25
 * Time: 22:07
 */
namespace App\Lib\Pool;

use EasySwoole\Component\Pool\AbstractPool;

class MysqlPool extends AbstractPool
{
    /**
     * 请在此处返回一个数据库链接实例
     * @return MysqlObject
     */
    protected function createObject()
    {
        //$conf = Config::getInstance()->getConf("MYSQL");
        $conf = \Yaconf::get("mysql");
        $dbConf = new \EasySwoole\Mysqli\Config($conf);
        return new MysqlObject($dbConf);
    }
}