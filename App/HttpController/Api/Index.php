<?php
/**
 * Description:
 * User: Jeremy.Ke
 * Time: 2019/7/9 12:57
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

class Index extends Base
{
    public function getVideo()
    {
        $db = Di::getInstance()->get('MYSQL');
        $result = $db->where("id", 1)->getOne("video");
        return $this->writeJson(200, 'OK', $result);
    }
    public function setredis()
    {
        //$redis = new \Redis();
        //$redis->connect('127.0.0.1',6379,5);
        //$redis->set('klp',25);
        //$res = Redis::getInstance()->set('kk','nihaoya');
        $res = Di::getInstance()->get('REDIS')->set('kk','nihaoya111');
        return $this->writeJson(200,'ok',$res);
    }

    public function getredis()
    {
        //$redis = new \Redis();
        //$redis->connect('127.0.0.1',6379,5);
        //$redis->set('klp',25);
        //$res = Redis::getInstance()->get('kk');
        $res = Di::getInstance()->get('REDIS')->get('kk');
        return $this->writeJson(200,'ok',$res);
    }

    public function getyaconf()
    {
        $res = \Yaconf::get('redis');
        return $this->writeJson(200,'ok',$res);
    }
    //消息队列生产者
    public function pub()
    {
        $params = $this->request()->getRequestParam();
        Di::getInstance()->get("REDIS")->rPush('task_list',$params['f']);
    }

    public function vod()
    {
        $obj = new \App\Lib\AliyunSDK\AliVod();
        $title = "test_video";
        $file_name = 'test.mp4';
        $res = $obj->createUploadVideo($title,$file_name);
        $uploadAddress = json_decode(base64_decode($res->UploadAddress),true);
        $uploadAuth = json_decode(base64_decode($res->UploadAuth),true);
        $obj->initOssClient($uploadAuth,$uploadAddress);
        $video_file= "/data/wwwroot/easyswoole/webroot/video/2019/07/50142729b680d5fb.mp4";
        $res_a = $obj->uploadLocalFile($uploadAddress,$video_file);
        var_dump($res_a);
    }

    public function getvod()
    {
        $obj = new \App\Lib\AliyunSDK\AliVod();
        $res = $obj->getPlayInfo("822ee366320a4cd797e528c7a67a5d2f");
        var_dump($res);
    }

    //传统读取mysql数据
    public function list(){

        $condition = [];
        if(!empty($this->params['cat_id'])){
            $condition['cat_id'] = intval($this->params['cat_id']);
        }
        $videoModel = new VideoModel();
        $data = $videoModel->getVideoData($condition,$this->params['page'],$this->params['size']);
        if(!empty($data['lists'])){
            foreach ($data['lists'] as &$list){
                $list['create_time'] = date("Ymd H:i:s",$list['create_time']);
                $list['video_duration'] = gmstrftime("%H:%M:%S");
            }
        }
        return $this->writeJson(200,'ok',$data);
    }
    //读取静态化api生成的json数据
    public function listJson(){
        $cat_id = !empty($this->params['cat_id'])?intval($this->params['cat_id']):0;
        $video_file = EASYSWOOLE_ROOT."/webroot/video/json/".$cat_id.'.json';
        $video_data = is_file($video_file)?file_get_contents($video_file):"";
        $video_data = !empty($video_data)?json_decode($video_data,true):[];
        //分页
        $count = count($video_data);
        $video_data = array_splice($video_data,$this->params['from'],$this->params['size']);

        return $this->writeJson(200,'ok',$this->getPageData($count,$video_data));
    }
    //读取swoole_table数据
    public function listCache(){
        $cat_id = !empty($this->params['cat_id'])?intval($this->params['cat_id']):0;
        $video_data = Cache::getInstance()->get("index_video_data_cat_id_".$cat_id);
        $video_data = !empty($video_data)?$video_data:[];
        //分页
        $count = count($video_data);
        $video_data = array_splice($video_data,$this->params['from'],$this->params['size']);

        return $this->writeJson(200,'ok',$this->getPageData($count,$video_data));
    }

    public function test()
    {
        file_put_contents(EASYSWOOLE_ROOT.'/webroot/video/json/',"sdfdsfdsgf");
    }

    public function getById()
    {
        $id = !empty($this->params['id'])?intval($this->params['id']):0;
        $videoModel = new VideoModel();
        $data = $videoModel->getById($id);
        $data['create_time'] = date("Ymd H:i:s",$data['create_time']);
        $data['video_duration'] = gmstrftime("%H:%M:%S");
        //异步统计播放数
        TaskManager::async(function () use($id){
            Di::getInstance()->get("REDIS")->zincrby(\Yaconf::get('redis.video_play_key'),1,$id);
        });
        return $this->writeJson(200,'ok',$data);
    }

    public function rank()
    {
        $res = Di::getInstance()->get("REDIS")->zrevrange(\Yaconf::get('redis.video_play_key'),0,-1,"withscores");
        return $this->writeJson(200,'ok',$res);
    }

    /**
     * elasticsearch 根据id搜索
     * @return bool
     */
    public function elsIndex()
    {
        $param = [
            'index'=>'my_video',
            'type'=>'_doc',
            'id'=>'1',
        ];
        $client = Di::getInstance()->get("ES");
        $res = $client->get($param);
        return $this->writeJson(200,'ok',$res);
    }

    /**
     * elasticsearch 模糊搜索
     * @return bool
     * @throws \Throwable
     */

    public function elsLike()
    {
        $param = [
            'index'=>'my_video',
            'type'=>'_doc',
            'body'=>[
                'query'=>[
                    'match'=>[
                        'name'=>'杰'
                    ],
                ],
            ],

        ];
        //$client = ClientBuilder::create()->setHosts(['127.0.0.1:9502'])->build();
        $client = Di::getInstance()->get("ES");
        $res = $client->search($param);
        return $this->writeJson(200,'ok',$res);
    }

    public function esBymodel()
    {
        $new_video_model = new EsVideo();
        $res = $new_video_model->serachByName($this->params['name']);
        return $this->writeJson(200,'ok',$res);
    }
}