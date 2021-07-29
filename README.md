# packages-page-cache

### 介绍
Laravel 页面静态化缓存

#### 安裝教程

    composer require rizhou/page-cache

#### 导出配置文件

    php artisan vendor:publish --provider="Rizhou\PageCache\LaravelServiceProvider"

#### 配置文件参数说明
    enable 是否启用
    except 过滤的路由，如：product/*
    cache_dir 保存的路径
#### 使用方法

打开app/Http/Kernel.php 向web中间件组添加一项

    protected $middlewareGroups = [
        'web' => [
            \Rizhou\PageCache\Middleware\PageCacheResponse::class,
            ...
        ],
    ];
如果你只想选择性的去缓存特定的请求，则可以向routeMiddleware添加路由别名映射

    protected $routeMiddleware = [
        'page-cache' => \Rizhou\PageCache\Middleware\PageCacheResponse::class,
        ...
    ];

#### 清除页面缓存

    php artisan page-cache:clear
    
最好将命令加入到任务调度中，这样就可以定时清除页面缓存


#### 自定义缓存条件

1.创建中间件
    
    php artisan make:middleware CacheResponse


2.继承原中间件，并重写以下方法

       <?php
       
       namespace App\Http\Middleware;
       
       use Illuminate\Http\Request\Request;
       use Illuminate\Http\Request\Response;
       use Rizhou\PageCache\Middleware\CacheResponse as BaseCacheResponse;
       
       class CacheResponse extends BaseCacheResponse
       {
           protected function needIfCache(Request $request, Response $response)
           {
               //通过自定义的条件只要该方法返回false 则不会进行缓存
               //如：
               if ($request->path() == '/product') {
                   return false;
               }
       
               return parent::needIfCache($request, $response);
           }
       }

    
