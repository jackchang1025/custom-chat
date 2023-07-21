<?php

namespace App\Http\Services\CrawlingStrategy;

interface CrawlingStrategyInterface
{
    public function crawl(string $url);

}
