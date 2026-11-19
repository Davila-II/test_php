<?php
/**
 * Fonctions utilitaires globales pour DjoKhouse SARL
 */

/**
 * Formate un montant financier (ex: 15 000 000 FCFA)
 */
function format_money(float|int|string|null $number, string $currency = 'FCFA'): string {
    if ($number === null || $number === '') {
        return '0 ' . $currency;
    }
    // Si c'est déjà une chaîne avec des lettres, la renvoyer telle quelle
    if (is_string($number) && !is_numeric(trim($number))) {
        return htmlspecialchars($number);
    }
    $clean = floatval($number);
    return number_format($clean, 0, ',', ' ') . ' ' . $currency;
}

/**
 * Nettoie une chaîne contre les attaques XSS
 */
function sanitize(?string $str): string {
    return htmlspecialchars(trim((string)$str), ENT_QUOTES, 'UTF-8');
}

/**
 * Tronque un texte au nombre de mots spécifié
 */
function truncate_words(string $text, int $limit = 25, string $end = '...'): string {
    $words = explode(' ', strip_tags($text));
    if (count($words) <= $limit) {
        return $text;
    }
    return implode(' ', array_slice($words, 0, $limit)) . $end;
}

/**
 * Génère un slug à partir d'une chaîne
 */
function slugify(string $text): string {
    // Remplacement des caractères accentués
    $text = transliterator_transliterate('Any-Latin; Latin-ASCII; Lower()', $text);
    // Supprimer les caractères non alphanumériques
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    return strtolower($text ?: 'n-a');
}

/**
 * Formate une date ISO ou MySQL en français lisible
 */
function format_date(?string $date, bool $withTime = false): string {
    if (empty($date)) {
        return '-';
    }
    $timestamp = strtotime($date);
    if (!$timestamp) {
        return htmlspecialchars($date);
    }

    $format = $withTime ? 'd/m/Y à H:i' : 'd/m/Y';
    return date($format, $timestamp);
}
