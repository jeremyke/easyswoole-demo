<?php
/**
 * Description:
 * User: Jeremy.Ke
 * Time: 2019/8/1 15:18
 */
namespace App\HttpController\Api;

use App\HttpController\Base;

class Category extends Base
{
    public function index()
    {
        $config = \Yaconf::get("category.cats");
        return $this->writeJson(200,'OK',$config);
    }
}