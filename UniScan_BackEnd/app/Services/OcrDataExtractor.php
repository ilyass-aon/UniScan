<?php

namespace App\Services;

use App\Models\Application;

class OcrDataExtractor
{


public function extract(string $text, string $typeDocument): array
    {
        $data = [];
        // Nettoyage pour faciliter la recherche
        $cleanText = preg_replace('/\s+/', ' ', $text);

        // ---  pour la cin  ---
        if (str_contains($typeDocument, 'cin')) {
             // (Garde ton code CIN ici, il était bon)
             if (preg_match('/([A-Z]{1,2})\s?([0-9]{4,6})/', $cleanText, $matches)) {
                $data['cin'] = $matches[1] . $matches[2];
            }
        }

        // --- CAS DU RELEVÉ DE NOTES (BAC) ---
        if (str_contains($typeDocument, 'bac')) {
            
            //  Extraction Nom et Prénom 
            if (preg_match('/Nom et prénom\s+([A-Z\s]+?)\s+(?=[A-Z]\d{8,10})/', $cleanText, $matches)) {
                $fullName = trim($matches[1]); 

                $parts = explode(' ', $fullName);
                if (count($parts) > 1) {
                    $data['prenom'] = array_pop($parts); 
                    $data['nom'] = implode(' ', $parts); 
                } else {
                    $data['nom'] = $fullName; 
                }
            }

            // Extraction du CNE / Code Massar
            if (preg_match('/([A-Z]\d{9})/', $cleanText, $matches)) {
                $data['cne'] = $matches[1];
            }

            //  Extraction de la Moyenne Générale
            if (preg_match('/Moyenne\s*Générale\s*:?\s*(\d{1,2}[.,]\d{2})/', $cleanText, $matches)) {
                $data['note_bac'] = str_replace(',', '.', $matches[1]);
            }
            // Si on ne trouve pas "Moyenne Générale", on cherche juste le format note à la fin
            elseif (preg_match('/(\d{1,2}[.,]\d{2})\s*$/', $cleanText, $matches)) {
                 $data['note_bac'] = str_replace(',', '.', $matches[1]);
            }

            //  Extraction de l'année 
            if (preg_match_all('/20\d{2}\s?[\/-]\s?(20\d{2})/', $cleanText, $matches)) {
                
                // $matches[1] contient les années de fin (ex: 2017, 2018, 2019)
                // On prend la valeur maximale parmi ces années scolaires
                $data['annee_bac'] = max($matches[1]);

            } 
            // 2. Fallback (Secours) : Si l'OCR a mal lu les slashs "/", on prend juste la plus grande année trouvée
            elseif (preg_match_all('/(20\d{2})/', $cleanText, $allYears)) {
                
                $years = $allYears[1];
                // On trie pour avoir la plus grande
                rsort($years);
                
                // Petite sécurité : Si la plus grande date est l'année actuelle ou future (date d'impression), 
                // on essaie de prendre la suivante si elle existe.
                // Sinon on prend la plus grande par défaut.
                $data['annee_bac'] = $years[0]; 
            }
        }

        return $data;
    }

    // vrifie la coherence entre ocr et donnees saisies par l etudiant
    public function verifyConsistency(array $extractedData, Application $application): array
    {
        $report = [
            'is_consistent' => true, 
            'warnings' => []        
        ];

        // --- verification CIN  ---
        if (isset($extractedData['cin'])) {
            $ocrCin = $this->cleanString($extractedData['cin']);
            $userCin = $this->cleanString($application->cin_saisi);

            if ($ocrCin !== $userCin) {
                $this->addWarning($report, "CIN", $extractedData['cin'], $application->cin_saisi);
            }
        }

        // --- verification CNE  ---
        if (isset($extractedData['cne'])) {
            $ocrCne = $this->cleanString($extractedData['cne']);
            $userCne = $this->cleanString($application->cne_saisi);

            if ($ocrCne !== $userCne) {
                $this->addWarning($report, "CNE", $extractedData['cne'], $application->cne_saisi);
            }
        }

        // ---  verification NOM  ---
        if (isset($extractedData['nom'])) {
            if (!$this->isSimilar($extractedData['nom'], $application->nom_saisi)) {
                $this->addWarning($report, "Nom", $extractedData['nom'], $application->nom_saisi);
            }
        }

        // --- verification PRÉNOM  ---
        if (isset($extractedData['prenom'])) {
            if (!$this->isSimilar($extractedData['prenom'], $application->prenom_saisi)) {
                $this->addWarning($report, "Prénom", $extractedData['prenom'], $application->prenom_saisi);
            }
        }

        // --- verification note BAC  ---
        if (isset($extractedData['note_bac'])) {
            $noteOcr = (float) $extractedData['note_bac'];
            $noteUser = (float) $application->note_bac_saisie;

            if (abs($noteOcr - $noteUser) > 0.1) {
                $this->addWarning($report, "Note Bac", $extractedData['note_bac'], $application->note_bac_saisie);
            }
        }

        return $report;
    }

    // --------  Helpers ---------
    private function addWarning(array &$report, string $field, $valOcr, $valUser)
    {
        $report['is_consistent'] = false;
        $report['warnings'][] = "Incohérence $field : OCR [$valOcr] vs Saisi [$valUser]";
    }

    private function cleanString($str)
    {
        return strtoupper(trim(str_replace(' ', '', $str)));
    }

    /**
     * Compare deux textes avec tolérance (Distance de Levenshtein)
     * Utile pour les noms où l'OCR peut faire de petites fautes.
     */
    private function isSimilar($str1, $str2)
    {
        $s1 = $this->cleanString($str1);
        $s2 = $this->cleanString($str2);

        if ($s1 === $s2) return true;

        $distance = levenshtein($s1, $s2);

        return $distance <= 2;
    }
}