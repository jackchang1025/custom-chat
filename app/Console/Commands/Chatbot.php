<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Mindwave\Mindwave\Agents\Agent;
use Mindwave\Mindwave\Facades\DocumentLoader;
use Mindwave\Mindwave\Facades\Mindwave;
use Mindwave\Mindwave\LLM\Drivers\OpenAIChat;
use Mindwave\Mindwave\Memory\ConversationBufferMemory;
use OpenAI;

class Chatbot extends Command
{
    public $signature = 'chat';

    public function handle()
    {

        $agent = Mindwave::agent(
            memory: ConversationBufferMemory::fromMessages([])
        );

        $pdf = DocumentLoader::fromPdf(
            data: \Storage::get('0kpb0rq96AJGeoJdauUi/8fJXW8z7MmnKfr4RbTJg.pdf')
        );

        $web = DocumentLoader::fromUrl(
            data: "https://www.far-seeing.com/",
            meta: ["name" => "Mindwave Documentation"],
        );

        $data = DocumentLoader::fromText("My name is Helge Sverre");


//        Mindwave::brain()
//            ->consume($pdf)
//            ->consume($web)
//            ->consume($data);


//        $agent->ask("深圳远古物流有限公司地址在哪里？");

        while (true) {

            $input = $this->ask('Prompt');
            $response = $agent->ask($input);

            $this->comment($response);
        }
    }
}
