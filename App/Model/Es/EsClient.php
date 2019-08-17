<?php
/**
 * Description:
 * User: Jeremy.Ke
 * Time: 2019/8/15 19:35
 */
namespace App\Model\Es;

use EasySwoole\Component\Singleton;
use Elasticsearch\ClientBuilder;

class EsClient
{
    use Singleton;
    public $esClient = null;
    private function __construct()
    {
        $es_conf = \Yaconf::get('es');
        $this->esClient = ClientBuilder::create()->setHosts([$es_conf['host'].":".$es_conf['port']])->build();
    }

    public function __call($name, $arguments)
    {
        // TODO: Implement __call() method.
        return $this->esClient->$name(...$arguments);
    }
}