<?php

namespace App\Jobs;

use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Google\Cloud\Vision\V1\Client\ImageAnnotatorClient;
use Google\Cloud\Vision\V1\AnnotateImageRequest;
use Google\Cloud\Vision\V1\Feature;
use Google\Cloud\Vision\V1\Image;
use Google\Cloud\Vision\V1\BatchAnnotateImagesRequest;
use App\Services\OcrDataExtractor;

class ProcessOcrDocument implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $document;

    public function __construct(Document $document)
    {
        $this->document = $document;
    }

    public function handle(): void
    {
        $credentialsPath = storage_path('app/google-credentials.json');

        if (!file_exists($credentialsPath)) {
            $this->document->update(['ocr_status' => 'failed', 'texte_ocr_brut' => "Credentials introuvables"]);
            return;
        }

        try {
            $imageAnnotator = new ImageAnnotatorClient([
                'credentials' => $credentialsPath
            ]);

            $imagePath = Storage::path($this->document->chemin_fichier);
            $imageContent = file_get_contents($imagePath);

            $image = new Image();
            $image->setContent($imageContent);

            $feature = new Feature();
            $feature->setType(Feature\Type::TEXT_DETECTION);

            $request = new AnnotateImageRequest();
            $request->setImage($image);
            $request->setFeatures([$feature]);

            $batchRequest = new BatchAnnotateImagesRequest();
            $batchRequest->setRequests([$request]);

            $response = $imageAnnotator->batchAnnotateImages($batchRequest);

            $imageResponse = $response->getResponses()[0];
            
            if ($imageResponse->hasError()) {
                $this->document->update([
                    'ocr_status' => 'failed',
                    'texte_ocr_brut' => "Erreur Google: " . $imageResponse->getError()->getMessage()
                ]);
                $imageAnnotator->close();
                return;
            }

            $annotations = $imageResponse->getTextAnnotations();

            if (count($annotations) > 0) {
                $fullText = $annotations[0]->getDescription();
                $extractor = new OcrDataExtractor();
                $extractedData = $extractor->extract($fullText, $this->document->type_document);

    
                $application = $this->document->application; 
    
                $consistencyReport = $extractor->verifyConsistency($extractedData, $application);

                $finalData = array_merge($extractedData, ['verification' => $consistencyReport]);

                
                $this->document->update([
                    'texte_ocr_brut' => $fullText,
                    'ocr_status' => 'success',
                    'data_extraite_json' => $finalData,
                ]);
            } else {
                $this->document->update([
                    'ocr_status' => 'failed',
                    'texte_ocr_brut' => "Aucun texte trouvé."
                ]);
            }

            $imageAnnotator->close();

        } catch (\Exception $e) {
            $this->document->update([
                'ocr_status' => 'failed',
                'texte_ocr_brut' => "Exception PHP : " . $e->getMessage()
            ]);
        }
    }
}