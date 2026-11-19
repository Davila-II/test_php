<?php
/**
 * SupabaseStorage — Upload de fichiers vers Supabase Storage
 * Fallback vers le filesystem local en développement
 */
class SupabaseStorage {

    private static string $bucket = 'djokhouse-media';

    /**
     * Upload un fichier (depuis $_FILES) et retourne son URL publique
     *
     * @param array  $file     Élément de $_FILES (ex: $_FILES['image'])
     * @param string $folder   Sous-dossier dans le bucket (ex: 'properties')
     * @return string          URL publique du fichier uploadé
     * @throws RuntimeException en cas d'erreur
     */
    public static function upload(array $file, string $folder = 'uploads'): string {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Erreur lors de l\'upload : code ' . $file['error']);
        }

        $supabaseUrl = getenv('SUPABASE_URL');
        $serviceKey  = getenv('SUPABASE_SERVICE_KEY');

        // --- Production : Supabase Storage ---
        if (!empty($supabaseUrl) && !empty($serviceKey)) {
            return self::uploadToSupabase($file, $folder, $supabaseUrl, $serviceKey);
        }

        // --- Local : filesystem ---
        return self::uploadLocal($file, $folder);
    }

    private static function uploadToSupabase(array $file, string $folder, string $url, string $key): string {
        $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = $folder . '/' . uniqid('djok_', true) . '.' . $ext;

        $apiUrl   = rtrim($url, '/') . '/storage/v1/object/' . self::$bucket . '/' . $filename;
        $mimeType = $file['type'] ?: 'application/octet-stream';

        $ch = curl_init($apiUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => file_get_contents($file['tmp_name']),
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $key,
                'Content-Type: ' . $mimeType,
                'x-upsert: true',
            ],
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 && $httpCode !== 201) {
            throw new RuntimeException('Supabase Storage error (' . $httpCode . '): ' . $response);
        }

        // URL publique
        return rtrim($url, '/') . '/storage/v1/object/public/' . self::$bucket . '/' . $filename;
    }

    private static function uploadLocal(array $file, string $folder): string {
        $uploadDir = __DIR__ . '/../images/uploads/' . $folder;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = uniqid('djok_', true) . '.' . $ext;
        $dest     = $uploadDir . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            throw new RuntimeException('Impossible de déplacer le fichier uploadé.');
        }

        return 'images/uploads/' . $folder . '/' . $filename;
    }

    /**
     * Supprimer un fichier du Storage Supabase
     */
    public static function delete(string $publicUrl): bool {
        $supabaseUrl = getenv('SUPABASE_URL');
        $serviceKey  = getenv('SUPABASE_SERVICE_KEY');

        if (empty($supabaseUrl) || empty($serviceKey)) {
            // Local : on supprime juste le fichier
            $path = __DIR__ . '/../' . ltrim(parse_url($publicUrl, PHP_URL_PATH), '/');
            return file_exists($path) && unlink($path);
        }

        // Extraire le chemin relatif depuis l'URL publique
        $prefix   = rtrim($supabaseUrl, '/') . '/storage/v1/object/public/' . self::$bucket . '/';
        $filePath = str_replace($prefix, '', $publicUrl);

        $apiUrl = rtrim($supabaseUrl, '/') . '/storage/v1/object/' . self::$bucket . '/' . $filePath;

        $ch = curl_init($apiUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => 'DELETE',
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $serviceKey,
            ],
        ]);
        $httpCode = curl_getinfo(curl_exec($ch) ? $ch : $ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode === 200;
    }
}
