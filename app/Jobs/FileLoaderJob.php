<?php

namespace App\Jobs;

use App\Http\Requests\CreateChatbotViaPdfFlowRequest;
use App\Models\Chatbot;
use App\Models\PdfDataSource;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mindwave\Mindwave\Facades\DocumentLoader;
use Mindwave\Mindwave\Facades\Mindwave;
use Ramsey\Uuid\Uuid;

class FileLoaderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private readonly Chatbot $bot)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(CreateChatbotViaPdfFlowRequest $request): void
    {
        $folderName = Str::random(20);
        $filesUrls = [];

        foreach ($request->file('pdffiles') as $file) {

            $extension = $file->getClientOriginalExtension();
            $fileName = Str::random(20) . '.' . $extension;
            $filesUrls[] = $fileName;

            $path = $file->storeAs($folderName, $fileName, ['disk' => 'shared_volume']);

            $document = DocumentLoader::loader($extension, Storage::get($path));

            if ($document !== null) {
                Mindwave::brain()->consume($document);
            }else{
                Storage::delete($path);
            }
        }

        $dataSource = new PdfDataSource();
        $dataSource->setChatbotId($this->bot->getId());
        $dataSource->setId(Uuid::uuid4());
        $dataSource->setFiles($filesUrls);
        $dataSource->setFolderName($folderName);
        $dataSource->save();
    }
}
