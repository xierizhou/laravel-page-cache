<?php


namespace Rizhou\PageCache;

use Illuminate\Filesystem\Filesystem;
class PageCache
{
    /**
     * @var \Illuminate\Http\Request $request
     */
    protected $request;

    /**
     * @var \Illuminate\Http\Response $response
     */
    protected $response;

    /**
     * @var \Illuminate\Filesystem\Filesystem $filesystem
     */
    protected $filesystem;

    /**
     * PageCache constructor.
     *
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;

    }




    /**
     * Determine if caching is required
     *
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    public function needIfCache($request){
        return $request->isMethod('GET') && !$request->ajax() && $this->enable() && !$this->inExceptArray($request);
    }

    /**
     * File caching of page content
     *
     * @param \Illuminate\Http\Response $response
     * @param \Illuminate\Http\Request $request
     * @return $this
     */
    public function cache($request,$response){
        $cacheFilePath = $this->getCacheFilePath($request);
        $dir = $this->getCacheDirectory();

        if(!$this->filesystem->exists($dir)){
            $this->filesystem->makeDirectory($dir,0755,true,true);
        }
        if($request->refresh == 'page-cache' || !$this->filesystem->exists($cacheFilePath)){
            $this->filesystem->put(
                $cacheFilePath,
                $response->getContent()
            );
        }

        $this->request = $request;

        $this->response = $response;

        return $this;

    }

    /**
     * Return to page content
     *
     * @param \Illuminate\Http\Response|null $response
     * @param \Illuminate\Http\Request|null $request
     * @return false|string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function response($request=null,$response=null){

        $request = $this->request?:$request;

        $response = $this->response?:$response;

        $cacheFilePath = $this->getCacheFilePath($request);

        if($this->filesystem->exists($cacheFilePath)  && $this->filesystem->isReadable($cacheFilePath) ){

            return $this->filesystem->get($cacheFilePath);

        }
        return $response->getContent();

    }

    /**
     * Gets the path to the storage
     *
     *
     * @return string
     */
    protected function getCacheDirectory(){
        return rtrim(config('page-cache.cache_dir'),'/');
    }

    /**
     * Get cache file name
     *
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    protected function getCacheFileName($request){
        $request->except('refresh');
        $query = http_build_query($request->except('refresh'));
        if($query){
            $query = '?'.$query;
        }
        $name = $request->getPathInfo().$query;

        return md5($name).'.php';
    }

    /**
     * Get cache file path
     * @param $request
     * @return string
     */
    protected function getCacheFilePath($request){
        $dir = $this->getCacheDirectory();
        $fileName = $this->getCacheFileName($request);

        return $dir.'/'.$fileName;
    }

    /**
     * Get function switch
     *
     * @return \Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed
     */
    public function enable(){
        return config('page-cache.enable');
    }

    /**
     * Route filtering
     *
     * @param $request
     * @return bool
     */
    public function inExceptArray($request)
    {
        $excepts = config('page-cache.except');

        foreach ($excepts as $except) {

            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if ($request->fullUrlIs($except) || $request->is($except)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Clear cache file
     * @param string $path
     * @return bool
     */
    public function clear($path=''){
        return $this->filesystem->cleanDirectory(rtrim(config('page-cache.cache_dir'),'/').'/'.$path);
    }

    /**
     * @param $request
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function getCacheResponse($request){
        $cacheFilePath = $this->getCacheFilePath($request);

        if($this->filesystem->exists($cacheFilePath)  && $this->filesystem->isReadable($cacheFilePath) ){
            require_once $cacheFilePath;exit;
            //return $this->filesystem->get($cacheFilePath);
        }
        return false;
    }




}
