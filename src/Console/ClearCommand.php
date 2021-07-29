<?php


namespace Rizhou\PageCache\Console;

use Illuminate\Console\Command;
use Rizhou\PageCache\PageCache;

class ClearCommand extends Command
{

    /**
     * @var PageCache
     */
    private $pageCache;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'page-cache:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear page cache';

    /**
     * Create a new command instance.
     *
     * @param PageCache $pageCache
     * @return void
     */
    public function __construct(PageCache $pageCache)
    {
        parent::__construct();

        $this->pageCache = $pageCache;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->pageCache->clear();
        $this->info('Cleared successfully!');
    }
}
