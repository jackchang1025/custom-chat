<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\UuidInterface;

/**
 * App\Models\ChatHistory
 *
 * @property int $id
 * @property string $chatbot_id
 * @property string|null $session_id
 * @property string $from
 * @property string $message
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Chatbot|null $chatbot
 * @method static \Illuminate\Database\Eloquent\Builder|ChatHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatHistory whereChatbotId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatHistory whereFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatHistory whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatHistory whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatHistory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ChatHistory extends Model
{
    use HasFactory;

    protected $fillable = ['chatbot_id', 'from', // user or bot
        'message', 'session_id'];

    public function chatbot()
    {
        return $this->belongsTo(Chatbot::class);
    }

    public function setId(UuidInterface $id): void
    {
        $this->id = $id;
    }

    public function isFromUser(): bool
    {
        return $this->from === 'user';
    }

    public function isFromBot(): bool
    {
        return $this->from === 'bot';
    }

    public function setFromUser(): void
    {
        $this->from = 'user';
    }

    public function setFromBot(): void
    {
        $this->from = 'bot';
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->created_at;
    }

    public function setChatbotId(UuidInterface $chatbotId): void
    {
        $this->chatbot_id = $chatbotId;
    }

    public function setSessionId(string $sessionId): void
    {
        $this->session_id = $sessionId;
    }

}
