<?php
/**
 * Description: 反射机制
 * User: Jeremy.Ke
 * Time: 2019/7/21 17:59
 */
namespace App\Lib;

class ClassArr
{
    /**
     * 反射对应的类文件
     * @return array
     */
    public function uploadClassStat() {
        return [
            "image" => "\App\Lib\Upload\Image",
            "video" => "\App\Lib\Upload\Video",
        ];
    }

    /**
     * 反射写法
     * @param $type 反射类型
     * @param $supportedClass 反射类数组
     * @param array $params 参数
     * @param bool $needInstance 是否需要实例化
     * @return bool|object
     * @throws \ReflectionException
     */
    public function initClass($type, $supportedClass, $params = [], $needInstance = true) {
        if(!array_key_exists($type, $supportedClass)) {
            return false;
        }

        $className = $supportedClass[$type];

        return $needInstance ? (new \ReflectionClass($className))->newInstanceArgs($params) : $className;
    }
}