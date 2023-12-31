<?php

namespace App\Models;

use App\Http\Enums\ChatBotInitialPromptEnum;
use App\Http\Enums\ChatbotStatusType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * App\Models\Chatbot
 *
 * @property string $id
 * @property string $name
 * @property string $token
 * @property string|null $website
 * @property string $status
 * @property string $prompt_message
 * @property int $enhanced_privacy
 * @property int $smart_sync
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ChatHistory> $messages
 * @property-read int|null $messages_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ChatbotSetting> $settings
 * @property-read int|null $settings_count
 * @method static \Illuminate\Database\Eloquent\Builder|Chatbot newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Chatbot newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Chatbot query()
 * @method static \Illuminate\Database\Eloquent\Builder|Chatbot whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chatbot whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chatbot whereEnhancedPrivacy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chatbot whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chatbot whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chatbot wherePromptMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chatbot whereSmartSync($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chatbot whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chatbot whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chatbot whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chatbot whereWebsite($value)
 * @mixin \Eloquent
 */
class Chatbot extends Model
{
    use HasFactory;

    public $incrementing = false;

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'string',
    ];

    public function getId(): UuidInterface
    {
        return Uuid::fromString($this->id);
    }

    public function setId(UuidInterface $id): void
    {
        $this->id = $id->toString();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getWebsite(): string
    {
        return $this->website;
    }

    public function setWebsite(string $website): void
    {
        $this->website = $website;
    }

    public function getStatus(): ChatbotStatusType
    {
        return new ChatbotStatusType($this->status);
    }

    public function setStatus(ChatbotStatusType $status): void
    {
        $this->status = $status->getStatus();
    }

    public function getPromptMessage(): ?string
    {
        return $this->prompt_message ?? ChatBotInitialPromptEnum::AI_ASSISTANT_INITIAL_PROMPT;
    }

    public function setPromptMessage(string $promptMessage): void
    {
        $this->prompt_message = $promptMessage;
    }

    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function settings(): HasMany
    {
        return $this->hasMany(ChatbotSetting::class);
    }

    public function crateOrUpdateSetting($name, $value): void
    {
        $setting = $this->settings()->where('name', $name)->first();
        if ($setting) {
            $setting->value = $value;
            $setting->save();
        } else {
            $setting = new ChatbotSetting();
            $setting->setId(Uuid::uuid4());
            $setting->setChatbotId($this->getId());
            $setting->setName($name);
            $setting->setValue($value);
            $setting->save();
        }
    }

    public function getSetting($name)
    {
        $setting = $this->settings()->where('name', $name)->first();
        if ($setting) {
            return $setting->value;
        }
        return null;
    }

    public function getWebsiteDataSources()
    {
        return $this->hasMany(WebsiteDataSource::class);
    }

    public function getPdfFilesDataSources()
    {
        return $this->hasMany(PdfDataSource::class);
    }

    public function getCodebaseDataSources()
    {
        return $this->hasMany(CodebaseDataSource::class);
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->created_at;
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatHistory::class);
    }
}
