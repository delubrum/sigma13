<?php

declare(strict_types=1);

namespace App\Domain\Preoperational\Web\Adapters;

use App\Domain\Preoperational\Models\PreoperationalItem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Lorisleiva\Actions\Concerns\AsAction;
use thiagoalessio\TesseractOCR\TesseractOCR;

 // Requires legacy dependency

final class UploadPhotoAdapter
{
    use AsAction;

    public function handle(Request $request): Response
    {
        $id = $request->integer('id');
        $q_id = $request->integer('q_id');
        $fileKey = "foto_{$q_id}";

        if (! $id || ! $q_id || ! $request->hasFile($fileKey)) {
            return response('', 400);
        }

        /** @var UploadedFile|null $file */
        $file = $request->file($fileKey);

        if (! $file) {
            return response('', 400);
        }

        // This matches the legacy upload path 'uploads/preoperational/'
        // Actually Laravel might use public disk but let's emulate the legacy
        $filename = uniqid('pre_')."_{$id}_{$q_id}.".$file->getClientOriginalExtension();
        $destPath = public_path("uploads/preoperational/{$filename}");

        $file->move(public_path('uploads/preoperational/'), $filename);

        $url = "uploads/preoperational/{$filename}";

        $dataToUpdate = ['url' => $url];
        $headers = [];

        $IDS_CON_OCR = [1, 52];
        if (in_array($q_id, $IDS_CON_OCR)) {
            try {
                // @phpstan-ignore class.notFound
                $tesseract = new TesseractOCR($destPath);
                if (file_exists('/usr/bin/tesseract')) {
                    // @phpstan-ignore method.notFound
                    $tesseract->executable('/usr/bin/tesseract');
                }
                // @phpstan-ignore method.notFound, method.notFound
                $rawText = $tesseract->psm(7)->allowlist('0123456789')->run();
                $valorDetectado = preg_replace('/\D/', '', (string) $rawText);

                if (strlen($valorDetectado) >= 1) {
                    $dataToUpdate['obs'] = $valorDetectado;
                    $headers['HX-Trigger'] = json_encode([
                        'ocr-success' => [
                            'q_id' => $q_id,
                            'valor' => $valorDetectado,
                            'tipo' => ($q_id === 1 ? 'Kilometraje' : 'Horómetro'),
                        ],
                        'showMessage' => [
                            'type' => 'success',
                            'message' => 'Foto procesada',
                            'close' => '',
                        ],
                    ]);
                }
            } catch (\Exception) {
                // Log and ignore
            }
        }

        PreoperationalItem::updateOrCreate(
            ['preop_id' => $id, 'question_id' => $q_id],
            $dataToUpdate
        );

        if (empty($headers['HX-Trigger'])) {
            $headers['HX-Trigger'] = json_encode([
                'showMessage' => [
                    'type' => 'success',
                    'message' => 'Foto procesada',
                    'close' => '',
                ],
            ]);
        }

        return response(
            '<img src="/'.htmlspecialchars($url).'?t='.time().'" class="w-full h-full object-cover">'
        )->withHeaders($headers);
    }

    public function asController(Request $request): Response
    {
        return $this->handle($request);
    }
}
