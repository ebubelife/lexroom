<?php

namespace App\Jobs;

use App\Models\EvidenceFile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;

class ProcessEvidenceFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $evidenceFileId;

    /**
     * Create a new job instance.
     */
    public function __construct($evidenceFileId)
    {
        $this->evidenceFileId = $evidenceFileId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $evidence = EvidenceFile::find($this->evidenceFileId);

        if (!$evidence) {
            Log::error("Evidence file not found for processing: {$this->evidenceFileId}");
            return;
        }

        try {
            $text = '';
            $extension = strtolower(pathinfo($evidence->original_filename, PATHINFO_EXTENSION));
            
            // Wait for file to be fully written
            if (!Storage::disk('local')->exists($evidence->path)) {
                Log::error("File not found on disk: {$evidence->path}");
                return;
            }

            $absolutePath = Storage::disk('local')->path($evidence->path);

            if ($extension === 'pdf') {
                $parser = new Parser();
                $pdf = $parser->parseFile($absolutePath);
                $text = $pdf->getText();
            } elseif (in_array($extension, ['txt', 'csv', 'md'])) {
                $text = file_get_contents($absolutePath);
            } else {
                // Unsupported type for text extraction at this moment (e.g. docs, images)
                Log::info("Skipping text extraction for unsupported file type: {$extension}");
                return;
            }

            // Limit text size to prevent exceeding token limits (e.g. roughly first 20,000 characters)
            if (strlen($text) > 20000) {
                $text = substr($text, 0, 20000) . "\n...[TRUNCATED_DUE_TO_LENGTH]";
            }

            // Save extracted text
            $evidence->update(['extracted_text' => trim($text)]);
            Log::info("Successfully extracted text for EvidenceFile ID: {$evidence->id}");
            
        } catch (\Exception $e) {
            Log::error("Error processing evidence file {$this->evidenceFileId}: " . $e->getMessage());
        }
    }
}
