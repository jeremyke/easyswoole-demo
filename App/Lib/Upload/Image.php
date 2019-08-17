<?php
/**
 * Description:
 * User: Jeremy.Ke
 * Time: 2019/7/21 17:28
 */
namespace App\Lib\Upload;

class Image extends Base{

    /**
     * fileType
     * @var string
     */
    public $fileType = "image";

    public $maxSize = 122;

    /**
     * 文件后缀的medaiType
     * @var [type]
     */
    public $fileExtTypes = [
        'png',
        'jpeg',
        // todo
    ];
}