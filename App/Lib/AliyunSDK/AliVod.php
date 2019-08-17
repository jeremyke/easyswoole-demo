<?php
/**
 * Description:
 * User: Jeremy.Ke
 * Time: 2019/7/22 20:42
 */
namespace App\Lib\AliyunSDK;

require_once EASYSWOOLE_ROOT.'/App/Lib/AliyunSDK/aliyun-php-sdk-core/Config.php';
require_once EASYSWOOLE_ROOT.'/App/Lib/AliyunSDK/aliyun-php-sdk-oss/autoload.php';

use vod\Request\V20170321 as vod;
use OSS\OssClient;
use OSS\Core\OssException;

class AliVod
{
    public $accessKeyId = "LTAIhOugTfNYoE9R";
    public $accessKeySecret = "sdqrOjEJ7deIEXRz9Ngw4DJ8ARYDp0";
    public $client;
    public $ossClient;
    //使用AK初始化VOD客户端
    public function __construct()
    {
        $regionId = 'cn-shanghai';
        $profile = \DefaultProfile::getProfile($regionId, $this->accessKeyId, $this->accessKeySecret);
        $this->client =  new \DefaultAcsClient($profile);
    }

    /**
     *  获取视频上传地址和凭证
     * @param $title
     * @param $file_name
     * @param array $other_param
     * @return mixed|\SimpleXMLElement
     * @throws \ClientException
     * @throws \ServerException
     */
    public function createUploadVideo($title,$file_name,$other_param = []) {
        $request = new vod\CreateUploadVideoRequest();
        $request->setTitle($title);        // 视频标题(必填参数)
        $request->setFileName($file_name); // 视频源文件名称，必须包含扩展名(必填参数)
        if(isset($other_param['des']) && !empty($other_param['des'])){
            $request->setDescription($other_param['des']);  // 视频源文件描述(可选)
        }
        if(isset($other_param['cover']) && !empty($other_param['cover'])){
            $request->setCoverURL($other_param['cover']);  // 自定义视频封面(可选)
        }
        if(isset($other_param['tag']) && !empty($other_param['tag'])){
            $request->setTags($other_param['tag']);  // 视频标签，多个用逗号分隔(可选)
        }
        return $this->client->getAcsResponse($request);
    }

    /**
     * 使用上传凭证和地址初始化OSS客户端
     * @param $uploadAuth
     * @param $uploadAddress
     * @throws OssException
     */
    public function initOssClient($uploadAuth, $uploadAddress) {
        $this->ossClient = new OssClient($uploadAuth['AccessKeyId'], $uploadAuth['AccessKeySecret'], $uploadAddress['Endpoint'],
            false, $uploadAuth['SecurityToken']);
        $this->ossClient->setTimeout(86400*7);    // 设置请求超时时间，单位秒，默认是5184000秒, 建议不要设置太小，如果上传文件很大，消耗的时间会比较长
        $this->ossClient->setConnectTimeout(10);  // 设置连接超时时间，单位秒，默认是10秒
    }

    /**
     * 上传本地文件
     * @param $uploadAddress
     * @param $localFile
     * @return mixed
     */
    function uploadLocalFile($uploadAddress, $localFile) {
        return $this->ossClient->uploadFile($uploadAddress['Bucket'], $uploadAddress['FileName'], $localFile);
    }

    /**
     * [获取视频信息]
     * @param int $vedio_id
     * @return array|mixed|\SimpleXMLElement
     * @throws \ClientException
     * @throws \ServerException
     */
    public function getPlayInfo($vedio_id = 0)
    {
        if(empty($vedio_id)){
            return [];
        }
        $request = new vod\GetPlayInfoRequest();
        $request->setVideoId($vedio_id);
        $request->setAcceptFormat("JSON");
        return $this->client->getAcsResponse($request);
    }
}