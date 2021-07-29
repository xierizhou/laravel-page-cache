<?php
namespace Rizhou\PageCache\Middleware;

use Closure;
use Rizhou\PageCache\PageCache;

class PageCacheResponse
{
    /**
     * @var PageCache
     */
    protected $pageCache;

    /**
     * BaseCacheResponse constructor.
     * @param PageCache $pageCache
     */
    public function __construct(PageCache $pageCache)
    {
        $this->pageCache = $pageCache;
    }

    /**
     * Handle an incoming request.
     *
     * @param $request
     * @param Closure $next
     * @return false|mixed|string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle($request, Closure $next){
        $response = $next($request);

        if($this->needIfCache($request,$response)){
            $content = $this->pageCache->cache($request,$response)->response();
            $response->setContent($content);

        }
        return $response;
    }

    /**
     * Cache condition
     *
     * @param $request
     * @param $response
     * @return bool
     */
    public function needIfCache($request,$response){

        return $this->pageCache->needIfCache($request,$response);

    }



}
