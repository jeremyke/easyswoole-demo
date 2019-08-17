<?php
/**
 * Description:
 * User: Jeremy.Ke
 * Time: 2019/8/1 17:58
 */
namespace App\Lib\Cache;

use App\Model\Video as videoModel;
use EasySwoole\Component\Di;
use EasySwoole\FastCache\Cache;

class Video
{
    public function setIndexVideo()
    {
        $catIds = array_keys(\Yaconf::get("category.cats"));
        array_unshift($catIds,0);
        $video_model = new videoModel();
        $type = \Yaconf::get("base.indexCacheFuc");
        foreach ($catIds as $catId){
            $condition = [];
            if(!empty($catId)){
                $condition['cat_id'] = $catId;
            }
            $data = $video_model->getVideoCacheData($condition);
            if(empty($data)){
                continue;
            }
            foreach ($data as &$list){
                $list['create_time'] = date("Ymd H:i:s",$list['create_time']);
                $list['video_duration'] = gmstrftime("%H:%M:%S");
            }
            switch ($type){
                case 'file':
                    //写入文件file_put_contents
                    if(!empty($data)){
                        $path = EASYSWOOLE_ROOT.'/webroot/video/json/';
                        if(!is_dir($path)) {
                            mkdir($path, 0777 , true);
                        }
                        $flag = file_put_contents($path.$catId.".json", json_encode($data));
                        if(!$flag){
                            echo "cat_id:".$catId.'put data error'.PHP_EOL;
                        }
                    }
                    break;
                case "cache":
                    //写入缓存swoole_table
                    Cache::getInstance()->set("index_video_data_cat_id_".$catId,$data);
                    break;
                case "redis":
                    //写入redis
                    Di::getInstance()->get("REDIS")->set("index_video_data_cat_id_".$catId,$data);
                    break;
                default:
                    break;
            }
        }
    }
}