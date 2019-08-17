<?php
/**
 * Description:模型的基础类
 * User: Jeremy.Ke
 * Time: 2019/7/21 19:24
 */
namespace App\Model;

use EasySwoole\Component\Di;

class Base
{
    public $db = "";
    public function __construct() {
        if(empty($this->tableName)) {
            throw new \Exception("table error");
        }
        $db = Di::getInstance()->get("MYSQL");
        if($db instanceof \MysqliDb) {
            $this->db = $db;
        } else {
            throw new \Exception("db error");
        }
    }

    /**
     * 添加
     * @param $data
     * @return bool
     * @throws \Exception
     */
    public function add($data) {
        if(empty($data) || !is_array($data)) {
            return false;
        }
        return $this->db->insert($this->tableName, $data);
    }
}