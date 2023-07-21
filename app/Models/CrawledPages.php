<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * App\Models\CrawledPages
 *
 * @property int $id
 * @property string $chatbot_id
 * @property string $website_data_source_id
 * @property string $url
 * @property string|null $title
 * @property string|null $status_code
 * @property string|null $aws_url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|CrawledPages newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CrawledPages newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CrawledPages query()
 * @method static \Illuminate\Database\Eloquent\Builder|CrawledPages whereAwsUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CrawledPages whereChatbotId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CrawledPages whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CrawledPages whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CrawledPages whereStatusCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CrawledPages whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CrawledPages whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CrawledPages whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CrawledPages whereWebsiteDataSourceId($value)
 * @mixin \Eloquent
 */
class CrawledPages extends Model
{
    use HasFactory;

    protected $table = 'crawled_pages';

    protected $fillable = [
        'id',
        'chatbot_id',
        'website_data_source_id',
        'url',
        'title',
        'status_code',
    ];

    public function getId(): UuidInterface
    {
        return Uuid::fromString($this->id);
    }

    public function getChatbotId(): UuidInterface
    {
        return Uuid::fromString($this->chatbot_id);
    }

    public function getWebsiteDataSourceId(): UuidInterface
    {
        return Uuid::fromString($this->website_data_source_id);
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }


    public function getStatusCode(): string
    {
        return $this->status_code;
    }

    public function setId(UuidInterface $id): void
    {
        $this->id = $id;
    }

    public function setChatbotId(UuidInterface $chatbotId): void
    {
        $this->chatbot_id = $chatbotId;
    }

    public function setWebsiteDataSourceId(UuidInterface $websiteDataSourceId): void
    {
        $this->website_data_source_id = $websiteDataSourceId;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function setStatusCode(?string $statusCode): void
    {
        $this->status_code = $statusCode;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->created_at;
    }
}
