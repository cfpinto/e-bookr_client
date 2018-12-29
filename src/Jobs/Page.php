<?php

namespace Ebookr\Client\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class Page implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var int 
     */
    protected $pageId;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $pageId)
    {
        $this->pageId = $pageId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
    }
}
