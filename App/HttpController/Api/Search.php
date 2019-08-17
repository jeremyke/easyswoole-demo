<?php
/**
 * Description:
 * User: Jeremy.Ke
 * Time: 2019/8/15 20:25
 */
namespace App\HttpController\Api;

use App\HttpController\Base;
use EasySwoole\Component\Di;
use App\Lib\Redis\Redis;
use App\Model\Video as VideoModel;
use EasySwoole\FastCache\Cache;
use EasySwoole\EasySwoole\Swoole\Task\TaskManager;
use Elasticsearch\ClientBuilder;
use App\Model\Es\EsVideo;

class Search extends Base
{
    /**
     * 检索服务API
     */
    public function Index()
    {
        $keyword = trim($this->params['keyword']);
        if(empty($keyword)){
            return $this->writeJson(200,"ok",$this->getPageData(0,[]));
        }
        $es_obj = new EsVideo();
        $res = $es_obj->serachByName($keyword,$this->params['from'],$this->params['size']);
        if(empty($res)){
            return $this->writeJson(200,"ok",$this->getPageData(0,[]));
        }
        $hits = $res['hits']['hits'];
        $total = $res['hits']['total']['value'];
        if($total==0){
            return $this->writeJson(200,"ok",$this->getPageData(0,[]));
        }
        foreach ($hits as $hit){
            $source = $hit['_source'];
            $resData[] = [
                'id'=>$hit['_id'],
                'name'=>$source['name'],
                'image'=>$source['image'],
                'uploader'=>$source['uploader'],
                'video_id'=>$source['video_id'],
                'video_id'=>$source['video_id'],
                'keywords'=>[$keyword],
            ];
        }
        return $this->writeJson(200,"ok",$this->getPageData($total,$resData));
    }
}