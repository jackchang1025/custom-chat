<?php

namespace App\Http\Services\CrawlingStrategy;

use App\Http\Events\CrawlEvent;
use App\Http\Services\ContentDecorator\ContentDecoratorInterface;
use App\Models\CrawledPages;
use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Throwable;

class DefaultCrawlingStrategy implements CrawlingStrategyInterface
{
    public function __construct(
        protected readonly ContentDecoratorInterface $contentDecorator,
        protected string                             $rootUrl,
        protected UuidInterface                      $chatBotId,
        protected UuidInterface                      $dataSourceId,
        protected int                                $maxPages = 15,
        protected array                              $crawledUrls = []
    )
    {
    }

    public function isCrawledUrlsSubMaxPages(): bool
    {
        return count($this->crawledUrls) >= $this->maxPages;
    }

    public function addCrawledUrls(string $url): string
    {
        return $this->crawledUrls[] = $url;
    }

    public function setCrawledUrls(array $crawledUrls = []): array
    {
        return $this->crawledUrls = $crawledUrls;
    }

    public function crawl(string $url): bool
    {
        if ($this->isCrawledUrlsSubMaxPages() || in_array($url, $this->crawledUrls)) {
            return false;
        }

        $this->crawledUrls[] = $url;

        try {

            $response = Http::get($url);

            if ($response->failed() || !$this->storeCrawledPageContentToLocal($response)) {
                return false;
            }

            $this->storeCrawledPageContentToDatabase($response, $url);

            $links = $this->contentDecorator->extractLinks($response->body(), $this->rootUrl);

            foreach ($links as $link) {

                $this->crawl($link);

                // Update crawling progress
                event(new CrawlEvent($this->dataSourceId, $this->calculateCrawlingProgress()));
            }

        } catch (Exception|Throwable $e) {
            // Ignore the exception and continue crawling other links

            Log::error("Ignore the exception and continue crawling other links error: " . $e->getMessage());
            return false;
        }

        return true;
    }

    public function storeCrawledPageContentToLocal(Response $response): bool|string
    {
        $normalizedText = $this->contentDecorator->getNormalizedContent($response->body());
        if (empty($normalizedText)) {
            return false;
        }

        $textPath = $this->dataSourceId . "/" . Str::random() . ".txt";

        return Storage::put($textPath, $normalizedText) ? $textPath : false;
    }

    public function storeCrawledPageContentToDatabase(Response $response, string $url): CrawledPages
    {
        $page = new CrawledPages();
        $page->setUrl($url);
        $page->setStatusCode($response->status());
        $page->setChatbotId($this->chatBotId);
        $page->setTitle($this->contentDecorator->getCrawledPageTitle($response->body()));
        $page->setId(Uuid::uuid4());
        $page->setWebsiteDataSourceId($this->dataSourceId);
        $page->save();

        return $page;
    }

    /**
     * 这个方法用于计算爬取进度
     * @return int|mixed
     */
    public function calculateCrawlingProgress(): mixed
    {
        if ($this->maxPages <= 0) {
            return 0; // Avoid division by zero
        }

        $progress = (count($this->crawledUrls) / $this->maxPages) * 100;
        // Cap the progress at 100%

        return min($progress, 100);
    }
}
