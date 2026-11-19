<?php
/**
 * Session Manager pour DjoKhouse SCI
 * 
 * Sur Vercel (serverless) : utilise des cookies JSON signés (HMAC-SHA256)
 * En local : utilise les sessions PHP standard
 */
class SessionManager {

    private static bool $started = false;
    private static array $data   = [];
    private static bool $useServerless = false;
    private static string $cookieName  = 'djok_sess';

    public static function start(): void {
        if (self::$started) return;

        // Vercel est serverless : pas de filesystem pour les sessions PHP
        // On détecte via la variable d'env SUPABASE_DB_URL
        self::$useServerless = !empty(getenv('SUPABASE_DB_URL'));

        if (self::$useServerless) {
            self::startCookieSession();
        } else {
            self::startPhpSession();
        }

        self::$started = true;
    }

    // ---- Sessions PHP standard (local) ----
    private static function startPhpSession(): void {
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            $sessPath = session_save_path();
            if (empty($sessPath) || !is_writable($sessPath)) {
                @session_save_path(sys_get_temp_dir());
            }
            @session_start();
        }
        self::$data = &$_SESSION;
    }

    // ---- Sessions cookie signées (Vercel) ----
    private static function startCookieSession(): void {
        $secret = getenv('APP_SECRET') ?: 'djokhouse-default-secret-change-me';
        $cookie = $_COOKIE[self::$cookieName] ?? '';

        if (!empty($cookie)) {
            // Format : base64(json).hmac
            $parts = explode('.', $cookie, 2);
            if (count($parts) === 2) {
                [$payload64, $sig] = $parts;
                $expected = hash_hmac('sha256', $payload64, $secret);
                if (hash_equals($expected, $sig)) {
                    $decoded = json_decode(base64_decode($payload64), true);
                    if (is_array($decoded)) {
                        self::$data = $decoded;
                    }
                }
            }
        }
    }

    private static function saveCookieSession(): void {
        if (!self::$useServerless || headers_sent()) return;

        $secret    = getenv('APP_SECRET') ?: 'djokhouse-default-secret-change-me';
        $payload64 = base64_encode(json_encode(self::$data));
        $sig       = hash_hmac('sha256', $payload64, $secret);
        $value     = $payload64 . '.' . $sig;

        // Cookie sécurisé, durée 8h, SameSite=Strict
        $expires   = time() + (60 * 60 * 8);
        setcookie(self::$cookieName, $value, [
            'expires'  => $expires,
            'path'     => '/',
            'secure'   => true,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }

    // ---- API publique ($_SESSION-like) ----

    public static function get(string $key, mixed $default = null): mixed {
        self::start();
        if (self::$useServerless) {
            return self::$data[$key] ?? $default;
        }
        return $_SESSION[$key] ?? $default;
    }

    public static function set(string $key, mixed $value): void {
        self::start();
        if (self::$useServerless) {
            self::$data[$key] = $value;
            self::saveCookieSession();
        } else {
            $_SESSION[$key] = $value;
        }
    }

    public static function has(string $key): bool {
        self::start();
        if (self::$useServerless) {
            return isset(self::$data[$key]);
        }
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void {
        self::start();
        if (self::$useServerless) {
            unset(self::$data[$key]);
            self::saveCookieSession();
        } else {
            unset($_SESSION[$key]);
        }
    }

    public static function destroy(): void {
        self::start();
        if (self::$useServerless) {
            self::$data = [];
            if (!headers_sent()) {
                setcookie(self::$cookieName, '', [
                    'expires'  => time() - 3600,
                    'path'     => '/',
                    'secure'   => true,
                    'httponly' => true,
                    'samesite' => 'Lax',
                ]);
            }
        } else {
            session_destroy();
        }
    }

    /**
     * Compatibilité : permet l'accès direct à $_SESSION dans le code legacy
     * en mode local (les deux fonctionnent ensemble)
     */
    public static function syncToGlobal(): void {
        if (!self::$useServerless) return;
        // Expose les données dans une variable globale accessible
        foreach (self::$data as $k => $v) {
            $_SESSION[$k] = $v;
        }
    }
}
