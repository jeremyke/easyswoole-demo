<?php
/**
 * Description:
 * User: Jeremy.Ke
 * Time: 2019/8/15 19:56
 */
namespace App\Model\Es;

use EasySwoole\Component\Singleton;
use Elasticsearch\ClientBuilder;
use EasySwoole\Component\Di;

class EsVideo extends EsBase
{
    public $index = "my_video";
    public $type = "_doc";
}