<?php
/**
 * Description:
 * User: Jeremy.Ke
 * Time: 2019/1/18 23:11
 */
namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\Controller;

class Base extends Controller
{
    /**
     * @var array 请求的参数
     */
    public $params = [];
    /**
     * 首页方法
     * @author : evalor <master@evalor.cn>
     */
    public function index()
    {

    }

    /**
     * 权限控制
     * @param $action 方法名
     * @return bool|null
     */
    protected function onRequest($action):?bool
    {
        $this->getParams();
        return true;
    }


    /**
     * 代码错误和谐提示
     * @param \Throwable $throwable
     * @param $actionName
     * @throws \Throwable
     */
    /*public function onException(\Throwable $throwable): void
    {
        $this->writeJson('500','请求不合法');
    }*/

    /**
     * 统一返回方法
     * @param $statusCode 状态码
     * @param $msg 返回信息
     * @param $result 返回数据
     * @return bool
     */
    public function writeJson($statusCode = 200,$msg = null,$result = null){
        if(!$this->response()->isEndResponse()){
            $data = Array(
                "code"=>$statusCode,
                "msg"=>$msg,
                "result"=>$result,
            );
            $this->response()->write(json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
            $this->response()->withHeader('Content-type','application/json;charset=utf-8');
            $this->response()->withStatus($statusCode);
            return true;
        }else{
            trigger_error("response has end");
            return false;
        }
    }

    public function getParams()
    {
        $param = $this->request()->getRequestParam();
        $param['page'] = !empty($param['page'])?intval($param['page']):1;
        $param['size'] = !empty($param['size'])?intval($param['size']):3;
        $param['from'] = ($param['page']-1)*$param['size'];
        $this->params = $param;
    }

    /**
     * 获取分页数据
     * @param $count [总数据量]
     * @param $data [分页数据]
     * @return array
     */
    public function getPageData($count,$data)
    {
        $total_page = ceil($count/$this->params['size']);
        $data = $data ?? [];
        return [
            'total_page' => $total_page,
            'page_size' => $this->params['page'],
            'count' => intval($count),
            'lists' => $data
        ];
    }
}