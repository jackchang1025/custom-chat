<?php

namespace App\Http\Listeners;

use App\Http\Enums\WebsiteDataSourceStatusType;
use App\Http\Events\WebsiteDataSourceCrawlingWasCompleted;
use App\Models\WebsiteDataSource;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Mindwave\Mindwave\Facades\DocumentLoader;
use Mindwave\Mindwave\Facades\Mindwave;

class IngestWebsiteDataSource implements ShouldQueue
{

    public string $queue = 'listeners';


    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function handle(WebsiteDataSourceCrawlingWasCompleted $event)
    {
        /** @var WebsiteDataSource $websiteDataSource */
        $websiteDataSource = WebsiteDataSource::findOrFail($event->getWebsiteDataSourceId());

        try {

            $files = Storage::files($websiteDataSource->getId());

            foreach ($files as $file){

                if ($document = DocumentLoader::fromText(Storage::get($file))) {
                    Mindwave::brain()->consume($document);
                }
            }

            $websiteDataSource->setCrawlingStatus(WebsiteDataSourceStatusType::COMPLETED);
            $websiteDataSource->save();
            Log::info("data source web site success ");

        } catch (Exception $e) {
            $websiteDataSource->setCrawlingStatus(WebsiteDataSourceStatusType::FAILED);
            $websiteDataSource->save();

            Log::error("data source web site error: ". $e->getMessage());
            return;
        }
    }
}
