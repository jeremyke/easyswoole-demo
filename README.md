# easyswoole 

是一款出来比较早的swoole框架，具有一定的生态，mvc模式更容易上手.

# 应用介绍
>在项目中我关键实现了以下功能点和解决方案

本项目，利用easyswoole框架搭建小视频点播的应用。静态化api,elasticsearch先结合使得搜索更加高性能，异步点赞，收藏功能，利用阿里云小视频
点播sdk，将视频源上传到阿里云服务器。

 - 多进程，异步任务，消息队列
 - 定时器，连接池，协程
 - 全局事件注册
 - elasticsearch搜索
 - yaconf配置文件插件
 - 应对高并发
 - 静态api缓存(redis，文件，swoole_table)
 - nginx+lua+easyswoole

# 文档

 - [3.x] https://www.easyswoole.com/Cn/Introduction/environment.html
 - [2.x] https://www.bookstack.cn/read/easySwoole-2.x-cn/Component-Spl-array.md
