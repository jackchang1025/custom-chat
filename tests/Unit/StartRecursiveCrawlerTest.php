<?php

namespace Tests\Unit;


use App\Http\Enums\WebsiteDataSourceStatusType;
use App\Http\Events\WebsiteDataSourceWasAdded;
use App\Http\Listeners\StartRecursiveCrawler;
use App\Http\Services\CrawlingStrategy\DefaultCrawlingStrategy;
use App\Models\WebsiteDataSource;
use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\TestCase;

class StartRecursiveCrawlerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->websiteDataSourceMock = Mockery::mock(WebsiteDataSource::class);
        $this->defaultCrawlingStrategyMock = Mockery::mock(DefaultCrawlingStrategy::class);
    }

    public function test_handle_website_data_source_was_added()
    {
        // Arrange
        $websiteDataSourceWasAddedMock = Mockery::mock(WebsiteDataSourceWasAdded::class);
        $websiteDataSourceWasAddedMock->shouldReceive('getWebsiteDataSourceId')->andReturn(1);
        $websiteDataSourceWasAddedMock->shouldReceive('getChatbotId')->andReturn(1);

        $this->websiteDataSourceMock->shouldReceive('find')->andReturn($this->websiteDataSourceMock);
        $this->websiteDataSourceMock->shouldReceive('getCrawlingStatus')->andReturn(WebsiteDataSourceStatusType::IN_PROGRESS);
        $this->websiteDataSourceMock->shouldReceive('getRootUrl')->andReturn('http://test.com');
        $this->websiteDataSourceMock->shouldReceive('setCrawlingStatus')->withArgs([WebsiteDataSourceStatusType::IN_PROGRESS])->andReturnNull();
        $this->websiteDataSourceMock->shouldReceive('save')->andReturnNull();

        $this->defaultCrawlingStrategyMock->shouldReceive('crawl')->andReturnNull();

        $startRecursiveCrawler = new StartRecursiveCrawler($this->websiteDataSourceMock, $this->defaultCrawlingStrategyMock);

        // Act
        $startRecursiveCrawler->handle($websiteDataSourceWasAddedMock);

        // Assert
        $this->websiteDataSourceMock->shouldHaveReceived('setCrawlingStatus')->withArgs([WebsiteDataSourceStatusType::IN_PROGRESS]);
        $this->websiteDataSourceMock->shouldHaveReceived('save');
    }

    public function test_handle_exception()
    {
        // Arrange
        $websiteDataSourceWasAddedMock = Mockery::mock(WebsiteDataSourceWasAdded::class);
        $websiteDataSourceWasAddedMock->shouldReceive('getWebsiteDataSourceId')->andReturn(1);
        $websiteDataSourceWasAddedMock->shouldReceive('getChatbotId')->andReturn(1);

        $this->websiteDataSourceMock->shouldReceive('find')->andReturn($this->websiteDataSourceMock);
        $this->websiteDataSourceMock->shouldReceive('getCrawlingStatus')->andReturn(WebsiteDataSourceStatusType::IN_PROGRESS);
        $this->websiteDataSourceMock->shouldReceive('getRootUrl')->andReturn('http://test.com');
        $this->websiteDataSourceMock->shouldReceive('setCrawlingStatus')->withArgs([WebsiteDataSourceStatusType::IN_PROGRESS])->andReturnNull();
        $this->websiteDataSourceMock->shouldReceive('save')->andReturnNull();

        $this->defaultCrawlingStrategyMock->shouldReceive('crawl')->andThrow(new \Exception("Test exception"));

        $startRecursiveCrawler = new StartRecursiveCrawler($this->websiteDataSourceMock, $this->defaultCrawlingStrategyMock);

        // Act
        $startRecursiveCrawler->handle($websiteDataSourceWasAddedMock);

        // Assert
        Log::shouldReceive('error')->once();
        $this->websiteDataSourceMock->shouldHaveReceived('setCrawlingStatus')->withArgs([WebsiteDataSourceStatusType::FAILED]);
        $this->websiteDataSourceMock->shouldHaveReceived('save');
    }
}
