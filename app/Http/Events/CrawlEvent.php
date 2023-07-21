<?php

namespace App\Http\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Ramsey\Uuid\UuidInterface;

class CrawlEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly UuidInterface $dataSourceId,public mixed $progress)
    {
    }
}
