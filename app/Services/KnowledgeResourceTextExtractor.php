<?php

namespace App\Services;

use App\Models\KnowledgeResource;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Smalot\PdfParser\Parser;
use ZipArchive;

class KnowledgeResourceTextExtractor
{
    public function extract(KnowledgeResource $resource): void
    {
        try {
            if (filled($resource->extracted_text)) {
                $resource->update(['extraction_status' => 'ready', 'extraction_error' => null, 'extracted_at' => now()]);
                return;
            }
            $path = Storage::disk($resource->file_disk)->path($resource->file_path);
            $extension = strtolower(pathinfo($resource->original_file_name, PATHINFO_EXTENSION));
            $text = match ($extension) {
                'txt' => file_get_contents($path) ?: '',
                'pdf' => (new Parser)->parseFile($path)->getText(),
                'docx' => $this->extractZipXml($path, ['word/document.xml']),
                'pptx' => $this->extractZipXml($path, ['ppt/slides/']),
                'xlsx' => $this->extractSpreadsheet($path),
                default => '',
            };

            $text = trim(preg_replace('/[ \t]+/', ' ', preg_replace('/\R{3,}/', "\n\n", strip_tags($text))));
            $resource->update([
                'extracted_text' => $text ?: null,
                'extraction_status' => $text ? 'ready' : 'unsupported',
                'extraction_error' => $text ? null : 'No machine-readable text could be extracted. Add source text manually.',
                'extracted_at' => now(),
            ]);
        } catch (\Throwable $e) {
            $resource->update(['extraction_status' => 'failed', 'extraction_error' => $e->getMessage()]);
        }
    }

    private function extractZipXml(string $path, array $prefixes): string
    {
        $zip = new ZipArchive;
        if ($zip->open($path) !== true) return '';
        $text = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if (!str_ends_with($name, '.xml') || !collect($prefixes)->contains(fn ($prefix) => str_starts_with($name, $prefix))) continue;
            $xml = $zip->getFromIndex($i);
            $text[] = preg_replace('/\s+/', ' ', strip_tags(str_replace(['</w:p>', '</a:p>'], "\n", $xml)));
        }
        $zip->close();
        return implode("\n", $text);
    }

    private function extractSpreadsheet(string $path): string
    {
        $book = IOFactory::load($path);
        $lines = [];
        foreach ($book->getWorksheetIterator() as $sheet) {
            $lines[] = $sheet->getTitle();
            foreach ($sheet->toArray(null, true, true, false) as $row) {
                $line = implode(' | ', array_filter(array_map('strval', $row), fn ($value) => trim($value) !== ''));
                if ($line !== '') $lines[] = $line;
            }
        }
        return implode("\n", $lines);
    }
}
