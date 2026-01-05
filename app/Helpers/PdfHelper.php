<?php

namespace App\Helpers;

class PdfHelper
{
    /**
     * Nettoie une valeur pour l'affichage dans un PDF
     */
    public static function clean($value): string
    {
        if (is_null($value)) {
            return '';
        }
        
        if (is_numeric($value)) {
            return (string) $value;
        }
        
        if (is_bool($value)) {
            return $value ? 'Oui' : 'Non';
        }
        
        $string = (string) $value;
        
        // Nettoyer avec iconv
        $string = @iconv('UTF-8', 'UTF-8//IGNORE', $string);
        if ($string === false) {
            $string = '';
        }
        
        // Supprimer les caractères de contrôle
        $string = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $string);
        
        // Vérifier l'encodage
        if (!mb_check_encoding($string, 'UTF-8')) {
            $string = mb_convert_encoding($string, 'UTF-8', 'UTF-8');
            $string = @iconv('UTF-8', 'UTF-8//IGNORE', $string);
            if ($string === false) {
                $string = '';
            }
        }
        
        return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', false);
    }
}

