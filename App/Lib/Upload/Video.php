<?php
namespace App\Lib\Upload;

class Video extends Base{

	/**
	 * fileType
	 * @var string
	 */
	public $fileType = "video";

	public $maxSize = 122;

	/**
	 * 文件后缀的medaiType
	 * @var [type]
	 */
	public $fileExtTypes = [
		'mp4',
		'x-flv',
		// todo
	];
}