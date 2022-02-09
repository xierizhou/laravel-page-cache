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

        $refresh = false;
        if($request->refresh == 'page-cache'){
            $refresh = true;
        }

        if(!$refresh && $content = $this->pageCache->getCacheResponse($request)){
            $response = response($content);
        }else{
            $response = $next($request);
            if($response->getStatusCode() == 200 && $this->pageCache->needIfCache($request)){
                $this->pageCache->cache($request,$response)->response();
            }

        }

        return $response;
    }




}
