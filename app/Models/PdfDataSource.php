<?php

namespace App\Models;

use App\Http\Enums\IngestStatusType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * App\Models\PdfDataSource
 *
 * @property string $id
 * @property string $chatbot_id
 * @property array $files
 * @property string|null $folder_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $ingest_status
 * @method static \Illuminate\Database\Eloquent\Builder|PdfDataSource newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PdfDataSource newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PdfDataSource query()
 * @method static \Illuminate\Database\Eloquent\Builder|PdfDataSource whereChatbotId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PdfDataSource whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PdfDataSource whereFiles($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PdfDataSource whereFolderName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PdfDataSource whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PdfDataSource whereIngestStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PdfDataSource whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PdfDataSource extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $casts = [
        'id' => 'string',
        'chatbot_id' => 'string',
        'files' => 'array',
    ];

    public function setChatbotId(UuidInterface $chatbotId): void
    {
        $this->chatbot_id = $chatbotId;
    }

    public function getId(): UuidInterface
    {
        return Uuid::fromString($this->id);
    }

    public function getChatbotId(): UuidInterface
    {
        return Uuid::fromString($this->chatbot_id);
    }

    public function setId(UuidInterface $id): void
    {
        $this->id = $id;
    }

    public function setFiles($files): void
    {
        $this->files = $files;
    }

    public function setFolderName($folderName): void
    {
        $this->folder_name = $folderName;
    }

    public function getFolderName(): string
    {
        return $this->folder_name;
    }

    public function getFiles()
    {
        return $this->files;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->created_at;
    }

    public function setStatus(string $status): void
    {
        $this->ingest_status = $status;
    }

    public function getStatus(): IngestStatusType
    {
        return new IngestStatusType($this->ingest_status);
    }
}
