<?php

namespace App\Http\Listeners;

use App\Http\Enums\IngestStatusType;
use App\Http\Events\PdfDataSourceWasAdded;
use App\Models\PdfDataSource;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Mindwave\Mindwave\Facades\DocumentLoader;
use Mindwave\Mindwave\Facades\Mindwave;

class IngestPdfDataSource implements ShouldQueue
{
    /**
     * @throws Exception
     */
    public function handle(PdfDataSourceWasAdded $event): void
    {
        try {

            $pdfDataSource = PdfDataSource::where('id', $event->getPdfDataSourceId())->firstOrFail();

            throw_unless($pdfDataSource->getFiles());

            $this->processPdfDataSource($pdfDataSource);

            $pdfDataSource->setStatus(IngestStatusType::SUCCESS);
            $pdfDataSource->save();

        } catch (Exception|\Throwable $e) {
            Log::error('Error processing PDF data source: ' . $e->getMessage());

            if ($e instanceof ModelNotFoundException) {
                return;
            }

            $pdfDataSource->setStatus(IngestStatusType::FAILED);
            $pdfDataSource->save();
        }
    }

    private function processPdfDataSource(PdfDataSource $pdfDataSource): void
    {
        foreach ($pdfDataSource->getFiles() as $file) {
            $filePath = "{$pdfDataSource->getFolderName()}/$file";

            if (!Storage::fileExists($filePath)) {
                continue;
            }

            $document = DocumentLoader::loader('pdf', Storage::get($filePath));

            if ($document === null) {
                Storage::delete($filePath);
                continue;
            }

            Mindwave::brain()->consume($document);
        }
    }
}
