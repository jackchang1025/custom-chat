<?php

namespace App\Http\Services;

use Mindwave\Mindwave\Memory\ConversationBufferMemory;

class ChatMessageHistoryLoader extends ConversationBufferMemory
{
    public static function fromMessages(array $messages): self
    {
        $instance = new self();


        foreach ($messages as $message) {

            dd($message);

            if ($message['role'] == 'assistant') {
                $instance->addAiMessage($message['content']);
            }

            if ($message['role'] == 'user') {
                $instance->addUserMessage($message['content']);
            }
        }

        return $instance;
    }
}
