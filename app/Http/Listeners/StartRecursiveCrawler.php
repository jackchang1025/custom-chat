<?php

namespace App\Http\Listeners;

use App\Http\Enums\WebsiteDataSourceStatusType;
use App\Http\Events\WebsiteDataSourceCrawlingWasCompleted;
use App\Http\Events\WebsiteDataSourceWasAdded;
use App\Http\Services\ContentDecorator\DefaultContentDecorator;
use App\Http\Services\CrawlingStrategy\DefaultCrawlingStrategy;
use App\Models\WebsiteDataSource;
use DOMDocument;
use Exception;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Throwable;

class StartRecursiveCrawler implements ShouldQueue,ShouldBeUnique
{
    public string $queue = 'listeners';

    public int $timeout = 99999;

    public int $tries = 3;

    public function handle(WebsiteDataSourceWasAdded $event)
    {

        /** @var WebsiteDataSource $dataSource */
        $dataSource = WebsiteDataSource::find($event->getWebsiteDataSourceId());
        $chatbotId = $event->getChatbotId();

        if ($dataSource->getCrawlingStatus()->isCompleted()) {
            return;
        }

        $defaultCrawlingStrategy = new DefaultCrawlingStrategy(
            new DefaultContentDecorator(new DOMDocument()),
            $dataSource->getRootUrl(),
            $event->getChatbotId(),
            $event->getWebsiteDataSourceId(),
            50
        );

        try {

            // Set the crawling status to "in progress"
            $dataSource->setCrawlingStatus(WebsiteDataSourceStatusType::IN_PROGRESS);
            $dataSource->save();

            // Start crawling from the root URL
            $defaultCrawlingStrategy->crawl($dataSource->getRootUrl());

            WebsiteDataSourceCrawlingWasCompleted::dispatch($chatbotId, $dataSource->getId());

        } catch (Exception|Throwable $exception) {

            Log::error("Start crawling error: " . $exception->getMessage());
            $dataSource->setCrawlingStatus(WebsiteDataSourceStatusType::FAILED);
            $dataSource->save();
        }
    }
}
