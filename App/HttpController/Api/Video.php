<?php
/**
 * Description:
 * User: Jeremy.Ke
 * Time: 2019/2/18 16:02
 */

namespace App\HttpController\Api;

use App\HttpController\Base;
use App\Model\Video as VideoModel;
use EasySwoole\EasySwoole\Logger;
use EasySwoole\Http\Message\Status;
use EasySwoole\Validate\Validate;

class Video extends Base
{
    public $logType = "video:";
    public function add()
    {
        //数据校验
        $params = $this->request()->getRequestParam();
        Logger::getInstance()->log($this->logType . "add:" .json_encode($params));
        $valitor = new Validate();
        $valitor->addColumn('name', "视频名称错误")->required('视频名称不能为空')->lengthMin(2, '最小长度不小于2')->lengthMax(20, '最大长度不能大于20');
        $valitor->addColumn('url', "视频地址错误")->required('视频地址参数缺失')->notEmpty('视频地址不能为空');
        $valitor->addColumn('image', "图片地址错误")->required('图片地址参数缺失')->notEmpty('图片地址不能为空');
        $valitor->addColumn('content', "视频描述错误")->required('视频描述参数缺失')->notEmpty('视频描述不能为空');
        $valitor->addColumn('cat_id', "栏目ID错误")->required('栏目ID参数缺失')->notEmpty('栏目ID不能为空');
        $validata = $valitor->validate($params);
        if(!$validata) {
            //print_r($validata->getErrorList());
            return $this->writeJson(Status::CODE_BAD_REQUEST, $valitor->getError()->__toString());
        }
        //数据组装
        $data = [
            'name'  => $params['name'],
            'cat_id'  => $params['cat_id'],
            'image'  => $params['image'],
            'url'  => $params['url'],
            'type'  => $params['type'],
            'content'  => $params['content'],
            'create_time'  => time(),
            'status'  => \Yaconf::get("status.normal"),
        ];
        //入库
        try{
            $model_obj = new VideoModel();
            $video_id = $model_obj->add($data);
            if(empty($video_id)){
                throw new \Exception("添加失败");
            }
            return $this->writeJson(Status::CODE_OK,"请求成功",['id'=>$video_id]);
        }catch (\Exception $e){
            return $this->writeJson(Status::CODE_BAD_REQUEST,$e->getMessage(),[]);
        }
    }
}