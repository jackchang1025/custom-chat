<?php

namespace App\Http\Listeners;

use App\Http\Events\CrawlEvent;
use App\Models\WebsiteDataSource;

class CrawlListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CrawlEvent $event): void
    {
        $dataSource = WebsiteDataSource::find($event->dataSourceId);
        $dataSource->setCrawlingProgress($event->progress);
        $dataSource->save();
    }
}
