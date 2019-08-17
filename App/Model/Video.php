<?php
/**
 * Description: video表的模型
 * User: Jeremy.Ke
 * Time: 2019/7/21 19:22
 */
namespace App\Model;

class Video extends Base
{
    public $tableName = "video";

    /**
     * 获取视频数据
     * @param array $conditon
     * @param int $page
     * @param int $size
     * @return array
     * @throws \Exception
     */
    public function getVideoData($conditon = [],$page=1,$size=10){
        if(!empty($size)){
            $this->db->pageLimit = $size;
        }
        if(!empty($conditon['cat_id'])){
            $this->db->where('cat_id',$conditon['cat_id']);
        }
        $this->db->where('status',1);
        $this->db->orderBy('id','desc');
        $res = $this->db->paginate($this->tableName, $page);
        //echo $this->db->getLastQuery();
        $data = [
            'total_pages'=>$this->db->totalPages,
            'page_size'=>$size,
            'count'=>intval($this->db->totalCount),
            'lists'=>$res,
        ];
        return $data;
    }

    /**
     * [获取视频cache]
     * @param array $conditon
     * @param int $page
     * @param int $size
     * @return array
     * @throws \Exception
     */
    public function getVideoCacheData($conditon = [],$size=1000){
        if(!empty($size)){
            $this->db->pageLimit = $size;
        }
        if(!empty($conditon['cat_id'])){
            $this->db->where('cat_id',$conditon['cat_id']);
        }
        $this->db->where('status',1);
        $this->db->orderBy('id','desc');
        $res = $this->db->paginate($this->tableName, 1);
        //echo $this->db->getLastQuery();
        return $res;
    }

    public function getById($id){
        if(empty($id)){
            return false;
        }
        $this->db->where('id',$id);
        $res = $this->db->getOne($this->tableName);
        //echo $this->db->getLastQuery();
        return $res;
    }
}