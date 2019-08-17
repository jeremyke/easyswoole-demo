<?php
/**
 * Description:
 * User: Jeremy.Ke
 * Time: 2019/8/15 20:15
 */
namespace App\Model\Es;

use EasySwoole\Component\Di;

class EsBase
{
    public $esClient = null;

    public function __construct()
    {
        $this->esClient = Di::getInstance()->get("ES");
    }

    public function serachByName($name,$from=0,$size=10,$matchType="match")
    {
        $name = trim($name);
        if(empty($name)){
            return [];
        }
        $param = [
            'index'=>$this->index,
            'type'=>$this->type,
            'body'=>[
                'query'=>[
                    $matchType=>[
                        'name'=>$name
                    ],
                ],
                'from'=>$from,
                'size'=>$size
            ],

        ];
        $res = $this->esClient->search($param);
        return $res;
    }
}