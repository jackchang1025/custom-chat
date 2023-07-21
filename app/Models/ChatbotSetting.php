<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\UuidInterface;

/**
 * App\Models\ChatbotSetting
 *
 * @property string $id
 * @property string $chatbot_id
 * @property string $name
 * @property string $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Chatbot|null $chatbot
 * @method static \Illuminate\Database\Eloquent\Builder|ChatbotSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatbotSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatbotSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatbotSetting whereChatbotId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatbotSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatbotSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatbotSetting whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatbotSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatbotSetting whereValue($value)
 * @mixin \Eloquent
 */
class ChatbotSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'chatbot_id',
        'name',
        'value',
    ];

    public $incrementing = false;

    protected $casts = [
        'id' => 'string',
        'chatbot_id' => 'string',
    ];

    public function setChatbotId(UuidInterface $chatbotId): void
    {
        $this->chatbot_id = $chatbotId;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function setId(UuidInterface $id): void
    {
        $this->id = $id;
    }


    public function getChatbotId(): UuidInterface
    {
        return $this->chatbot_id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function chatbot()
    {
        return $this->belongsTo(Chatbot::class);
    }

}
