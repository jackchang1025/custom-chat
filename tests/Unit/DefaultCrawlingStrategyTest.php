<?php

namespace Tests\Unit;

use App\Http\Services\ContentDecorator\ContentDecoratorInterface;
use App\Http\Services\CrawlingStrategy\DefaultCrawlingStrategy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\MockObject\MockObject;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Tests\TestCase;

class DefaultCrawlingStrategyTest extends TestCase
{
    use RefreshDatabase;
    protected ContentDecoratorInterface|MockObject $contentDecorator;
    protected string $rootUrl;
    protected UuidInterface $chatBotId;
    protected UuidInterface $dataSourceId;
    protected int $maxPages;
    protected array $crawledUrls;

    public function setUp(): void
    {
        parent::setUp();

        // TODO: Setup your dependencies here. For example:
        $this->contentDecorator = $this->createMock(ContentDecoratorInterface::class);
        $this->rootUrl = 'https://www.far-seeing.com';
        $this->chatBotId = Uuid::uuid4();
        $this->dataSourceId = Uuid::uuid4();
        $this->maxPages = 10;
        $this->crawledUrls = [];
    }

    /**
     * @return void
     */
    public function test_crawl_website()
    {
        $strategy = new DefaultCrawlingStrategy(
            $this->contentDecorator,
            $this->rootUrl,
            $this->chatBotId,
            $this->dataSourceId,
            $this->maxPages,
            $this->crawledUrls
        );

        // Mock the HTTP GET request
        Http::fake([
            $this->rootUrl => Http::response('<html lang=""></html>'),
        ]);

        $this->contentDecorator
            ->expects($this->once())
            ->method('getNormalizedContent')
            ->willReturn('Normalized Content');

        // Assuming that the crawl() method does not return anything
        $this->assertTrue($strategy->crawl($this->rootUrl));

        // Assert that a GET request was made to the correct URL
        Http::assertSent(function (Request $request) {
            return $request->url() == $this->rootUrl;
        });

        Storage::assertExists($this->dataSourceId);

        // Assert that a record was created in the database
        $this->assertDatabaseHas('crawled_pages', [
            'url' => $this->rootUrl,
            'chatbot_id' => $this->chatBotId,
            'website_data_source_id' => $this->dataSourceId,
        ]);


    }

    public function test_is_crawled_urls_sub_max_pages(){

        $strategy = new DefaultCrawlingStrategy(
            $this->contentDecorator,
            $this->rootUrl,
            $this->chatBotId,
            $this->dataSourceId,
            $this->maxPages,
            $this->crawledUrls
        );

        $this->assertFalse($strategy->isCrawledUrlsSubMaxPages());

        for ($i = 0; $i < 15; $i++) {
            $strategy->addCrawledUrls((string)$i);
        }

        $this->assertTrue($strategy->isCrawledUrlsSubMaxPages());
    }

    public function test_store_crawled_page_content_to_database()
    {
        $strategy = new DefaultCrawlingStrategy(
            $this->contentDecorator,
            $this->rootUrl,
            $this->chatBotId,
            $this->dataSourceId,
            $this->maxPages,
            $this->crawledUrls
        );

        // Mock the HTTP GET request
        Http::fake([
            $this->rootUrl => Http::response('<html lang=""><title>Test</title></html>'),
        ]);

        // Create a fake response
        $response  = Http::get($this->rootUrl);

        // Mock the ContentDecoratorInterface
        $this->contentDecorator
            ->expects($this->once())
            ->method('getCrawledPageTitle')
            ->willReturn('Test Title');

        // Call the method
        $strategy->storeCrawledPageContentToDatabase($response, $this->rootUrl);

        // Assert that a record was created in the database
        $this->assertDatabaseHas('crawled_pages', [
            'url' => $this->rootUrl,
            'chatbot_id' => $this->chatBotId,
            'website_data_source_id' => $this->dataSourceId,
            'status_code' => 200,
            'title' => 'Test Title'
        ]);
    }

    public function test_store_crawled_page_content_to_local(){

        $strategy = new DefaultCrawlingStrategy(
            $this->contentDecorator,
            $this->rootUrl,
            $this->chatBotId,
            $this->dataSourceId,
            $this->maxPages,
            $this->crawledUrls
        );

        // Mock the HTTP GET request
        Http::fake([
            $this->rootUrl => Http::response('<html lang=""><title>Test</title></html>'),
        ]);

        // Create a fake response
        $response  = Http::get($this->rootUrl);

        // Mock the ContentDecoratorInterface
        $this->contentDecorator
            ->expects($this->once())
            ->method('getNormalizedContent')
            ->willReturn('Normalized Content');

        $testPath = $strategy->storeCrawledPageContentToLocal($response);
        Storage::assertExists($testPath);

        $this->assertEquals('Normalized Content',Storage::get($testPath));
    }

    public function test_calculate_crawling_progress()
    {
        $strategy = new DefaultCrawlingStrategy(
            $this->contentDecorator,
            $this->rootUrl,
            $this->chatBotId,
            $this->dataSourceId,
            $this->maxPages,
            $this->crawledUrls
        );

        // When no URLs have been crawled, the progress should be 0
        $this->assertEquals(0, $strategy->calculateCrawlingProgress());

        // When some URLs have been crawled, the progress should be proportionate
        $strategy = new DefaultCrawlingStrategy(
            $this->contentDecorator,
            $this->rootUrl,
            $this->chatBotId,
            $this->dataSourceId,
            $this->maxPages,
            array_fill(0, 5, $this->rootUrl)  // Assume that 5 URLs have been crawled
        );
        $this->assertEquals(50, $strategy->calculateCrawlingProgress());

        // When the number of crawled URLs exceeds maxPages, the progress should be capped at 100
        $strategy = new DefaultCrawlingStrategy(
            $this->contentDecorator,
            $this->rootUrl,
            $this->chatBotId,
            $this->dataSourceId,
            $this->maxPages,
            array_fill(0, 20, $this->rootUrl)  // Assume that 20 URLs have been crawled
        );
        $this->assertEquals(100, $strategy->calculateCrawlingProgress());
    }
}
