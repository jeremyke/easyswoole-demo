<?php
/**
 * Description:
 * User: Jeremy.Ke
 * Time: 2019/2/14 14:13
 */
namespace App\HttpController\Api;

use App\HttpController\Base;
use EasySwoole\Component\Di;
use App\Lib\Upload\Video;
use App\Lib\Upload\Image;
use App\Lib\ClassArr;

class Upload extends Base
{
    public function file()
    {
        try{
            $request = $this->request();
            $files = $request->getSwooleRequest()->files;
            $types = array_keys($files);
            $type = $types[0];
            if(empty($type)) {
                return $this->writeJson(400, '上传文件不合法');
            }
            //$video_obj = new Video($request,$type);
            //$file = $video_obj->upload();
            $classObj = new ClassArr();
            $classStats = $classObj->uploadClassStat();
            $uploadObj = $classObj->initClass($type, $classStats, [$request, $type]);
            $file = $uploadObj->upload();
            if(empty($file)){
                throw new \Exception('上传失败');
            }
            $data = [
              'url' =>  $file,
            ];
            return $this->writeJson(200,"上传成功",$data);
        }catch (\Exception $e){
            return $this->writeJson(400,$e->getMessage(),[]);
        }

    }

    public function image()
    {
        try{
            $request = $this->request();
            $files = $request->getSwooleRequest()->files;
            $types = array_keys($files);
            $type = $types[0];
            if(empty($type)) {
                return $this->writeJson(400, '上传文件不合法');
            }
            $image_obj = new Image($request,$type);
            $file = $image_obj->upload();
            if(empty($file)){
                throw new \Exception('上传失败');
            }
            $data = [
                'url' =>  $file,
            ];
            return $this->writeJson(200,"上传成功",$data);
        }catch (\Exception $e){
            return $this->writeJson(400,$e->getMessage(),[]);
        }
    }
}