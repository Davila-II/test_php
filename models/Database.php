<?php
/**
 * Database connection & ORM helper for DjoKhouse SCI
 * 
 * Supporte :
 *  - PostgreSQL (Supabase) en production via SUPABASE_DB_URL
 *  - SQLite en local (fallback automatique)
 */
class Database {
    private static ?PDO $pdo = null;
    private static string $driver = 'sqlite';

    public static function getConn(): PDO {
        if (self::$pdo === null) {
            self::$pdo = self::connect();
        }
        return self::$pdo;
    }

    private static function connect(): PDO {
        // --- Production : Supabase PostgreSQL ---
        $dbUrl = getenv('SUPABASE_DB_URL');

        if (!empty($dbUrl)) {
            // Parse postgresql://user:pass@host:port/dbname
            $parts = parse_url($dbUrl);
            $host   = $parts['host'] ?? 'localhost';
            $port   = $parts['port'] ?? 5432;
            $dbname = ltrim($parts['path'] ?? '/postgres', '/');
            $user   = urldecode($parts['user'] ?? 'postgres');
            $pass   = urldecode($parts['pass'] ?? '');

            // Fallback port si query param pgbouncer/pooler
            if (isset($parts['query'])) {
                parse_str($parts['query'], $qParams);
                if (!empty($qParams['sslmode'])) {
                    // sslmode already specified
                }
            }

            $dsn = "pgsql:host={$host};port={$port};dbname={$dbname};sslmode=require";

            try {
                $pdo = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_PERSISTENT         => false,
                    PDO::ATTR_TIMEOUT            => 5,
                ]);

                self::$driver = 'pgsql';
                return $pdo;
            } catch (PDOException $e) {
                // En cas d'erreur de connexion à Supabase, afficher un message d'erreur explicite
                header('HTTP/1.1 500 Internal Server Error');
                die("<h3>Erreur de connexion Supabase (PostgreSQL) :</h3><pre>" . htmlspecialchars($e->getMessage()) . "</pre><p>Vérifiez SUPABASE_DB_URL dans les variables d'environnement Vercel.</p>");
            }
        }

        // --- Local : SQLite (développement) ---
        $dbDir = __DIR__ . '/../data';
        if (!is_dir($dbDir)) {
            mkdir($dbDir, 0755, true);
        }
        $dbPath = $dbDir . '/djokhouse.db';

        $pdo = new PDO('sqlite:' . $dbPath, null, null, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        self::$driver = 'sqlite';

        // Auto-schema + seed en local uniquement
        self::initSchema($pdo);
        self::seedData($pdo);

        return $pdo;
    }

    public static function driver(): string {
        return self::$driver;
    }

    public static function isPgsql(): bool {
        self::getConn(); // S'assure que la connexion est établie
        return self::$driver === 'pgsql';
    }

    // ----------------------------------------------------------------
    // Schema & Seed (SQLite local seulement — Supabase utilise le SQL du guide)
    // ----------------------------------------------------------------

    public static function initSchema(PDO $db): void {
        $db->exec("
            CREATE TABLE IF NOT EXISTS site_settings (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                site_name TEXT NOT NULL,
                slogan TEXT,
                phone TEXT,
                phone2 TEXT,
                email TEXT,
                address TEXT,
                about_summary TEXT,
                facebook TEXT,
                linkedin TEXT,
                whatsapp TEXT
            );

            CREATE TABLE IF NOT EXISTS admin_users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT UNIQUE NOT NULL,
                password_hash TEXT NOT NULL,
                email TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS services (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                slug TEXT UNIQUE NOT NULL,
                title TEXT NOT NULL,
                summary TEXT,
                content TEXT,
                image TEXT,
                icon TEXT,
                order_num INTEGER DEFAULT 0,
                is_active INTEGER DEFAULT 1
            );

            CREATE TABLE IF NOT EXISTS properties (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                slug TEXT,
                category TEXT NOT NULL,
                type TEXT DEFAULT 'Vente',
                location TEXT,
                surface TEXT,
                price TEXT,
                description TEXT,
                image TEXT,
                gallery TEXT,
                is_featured INTEGER DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS testimonials (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                author TEXT NOT NULL,
                role TEXT,
                avatar TEXT,
                content TEXT NOT NULL,
                rating INTEGER DEFAULT 5,
                is_active INTEGER DEFAULT 1
            );

            CREATE TABLE IF NOT EXISTS messages (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                email TEXT NOT NULL,
                phone TEXT,
                subject TEXT,
                message TEXT NOT NULL,
                is_read INTEGER DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS reservations (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                property_id INTEGER NULL,
                service_id INTEGER NULL,
                name TEXT NOT NULL,
                email TEXT NOT NULL,
                phone TEXT NOT NULL,
                details TEXT,
                status TEXT DEFAULT 'En attente',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS newsletter (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                email TEXT UNIQUE NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ");
    }

    public static function seedData(PDO $db): void {
        // Site settings
        $count = $db->query("SELECT COUNT(*) FROM site_settings")->fetchColumn();
        if ($count == 0) {
            $stmt = $db->prepare("
                INSERT INTO site_settings (site_name, slogan, phone, phone2, email, address, about_summary, facebook, linkedin, whatsapp)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                'DjoKhouse SCI',
                'Un Camerounais, un terrain',
                '(+237) 696 052 390',
                '(+237) 670 123 456',
                'contact@djokhouse.cm',
                'Douala, Cameroun',
                'DjoKhouse SCI est votre partenaire de confiance au Cameroun pour l\'acquisition, la sécurisation foncière, les études topographiques et l\'urbanisme.',
                'https://facebook.com',
                'https://linkedin.com',
                ''
            ]);
        }

        // Admin user
        $adminCount = $db->query("SELECT COUNT(*) FROM admin_users")->fetchColumn();
        if ($adminCount == 0) {
            $stmt = $db->prepare("INSERT INTO admin_users (username, password_hash, email) VALUES (?, ?, ?)");
            $stmt->execute(['admin', password_hash('admin123', PASSWORD_BCRYPT), 'admin@djokhouse.cm']);
        }

        // Services
        $srvCount = $db->query("SELECT COUNT(*) FROM services")->fetchColumn();
        if ($srvCount == 0) {
            $services = [
                ['conseils',    'Conseils Fonciers & Immobiliers', 'Expertise en droit foncier et accompagnement.', 'fal fa-balance-scale', 1],
                ['urbanisme',   'Urbanisme & Aménagement', 'Plans d\'aménagement, permis de construire et études urbaines.', 'fal fa-city', 2],
                ['topographie', 'Topographie & Génie Civil', 'Levés topographiques, plans d\'implantation et VRD.', 'fal fa-compass', 3],
                ['bornage',     'Bornage & Sécurisation Foncière', 'Bornage officiel, titre foncier et sécurisation.', 'fal fa-map-marked-alt', 4],
                ['prestation',  'Prestation de Services & BTP', 'Suivi de chantiers, maîtrise d\'œuvre et construction.', 'fal fa-tools', 5],
            ];
            $stmt = $db->prepare("INSERT INTO services (slug, title, summary, icon, order_num) VALUES (?, ?, ?, ?, ?)");
            foreach ($services as $s) $stmt->execute($s);
        }

        // Properties
        $propCount = $db->query("SELECT COUNT(*) FROM properties")->fetchColumn();
        if ($propCount == 0) {
            $properties = [
                ['Terrains Résidentiels PK14 - Douala', 'terrains-pk14-douala', 'Terrain', 'Vente', 'PK14, Douala', '500 m²', '15 000 000 FCFA', 'Terrains titrés en zone résidentielle calme, viabilisés, accès bitumé.', 'images/folio/4.jpg', 1],
                ['Lotissement Moderne Yassa - Douala', 'lotissement-yassa-douala', 'Urbanisme', 'En cours', 'Yassa, Douala', '1 200 m²', 'Sur demande', 'Projet de lotissement avec plan masse approuvé et voiries aménagées.', 'images/folio/5.jpg', 1],
                ['Bornage et Titre Foncier Kribi Bord de Mer', 'bornage-kribi', 'Bornage', 'Projet réalisé', 'Kribi', '800 m²', 'Réalisé avec succès', 'Bornage officiel et obtention du titre foncier.', 'images/folio/8.jpg', 1],
                ['Levé Topographique & VRD Yaoundé Olembe', 'leve-topo-olembe', 'Topographie', 'Projet réalisé', 'Olembe, Yaoundé', '3 500 m²', 'Réalisé avec succès', 'Levé topographique complet pour complexe immobilier.', 'images/folio/6.jpg', 1],
                ['Parcelles Agricoles & Écologiques Dibombari', 'parcelles-agricoles-dibombari', 'Terrain', 'Vente', 'Dibombari, Littoral', '20 000 m²', '2 500 FCFA / m²', 'Idéal pour projets agropastoraux. Sol fertile.', 'images/folio/7.jpg', 1],
                ['Résidence Art Family - Bonamoussadi', 'residence-art-family-bonamoussadi', 'Maison', 'Vente', 'Bonamoussadi, Douala', '350 m²', '65 000 000 FCFA', 'Villa contemporaine 4 chambres, finitions soignées.', 'images/folio/9.jpg', 1],
            ];
            $stmt = $db->prepare("INSERT INTO properties (title, slug, category, type, location, surface, price, description, image, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            foreach ($properties as $p) $stmt->execute($p);
        }

        // Testimonials
        $testiCount = $db->query("SELECT COUNT(*) FROM testimonials")->fetchColumn();
        if ($testiCount == 0) {
            $testimonials = [
                ['Antoine Kamga', 'Propriétaire à Douala', 'images/avatar/user.png', 'Je remercie chaleureusement DjoKhouse SCI pour le suivi transparent et rigoureux lors de l\'acquisition de mon terrain !', 5],
                ['Marie-Claire Ngo', 'Membre de la Diaspora (France)', 'images/avatar/user.png', 'DjoKhouse SCI a sécurisé mon achat depuis la France avec un titre foncier authentique. La promesse est tenue !', 5],
                ['Patrick Mbida', 'Promoteur Immobilier', 'images/avatar/user.png', 'La précision des levés topographiques et l\'efficacité pour le bornage m\'ont fait gagner un temps précieux.', 5],
            ];
            $stmt = $db->prepare("INSERT INTO testimonials (author, role, avatar, content, rating) VALUES (?, ?, ?, ?, ?)");
            foreach ($testimonials as $t) $stmt->execute($t);
        }
    }

    // ----------------------------------------------------------------
    // Méthodes ORM génériques
    // ----------------------------------------------------------------

    public static function query(string $sql, array $params = []): PDOStatement {
        $stmt = self::getConn()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function fetchAll(string $sql, array $params = []): array {
        return self::query($sql, $params)->fetchAll();
    }

    public static function fetchOne(string $sql, array $params = []): ?array {
        $result = self::query($sql, $params)->fetch();
        return $result ?: null;
    }

    public static function insert(string $table, array $data): int {
        $fields       = array_keys($data);
        $placeholders = array_fill(0, count($fields), '?');
        $sql = "INSERT INTO {$table} (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";

        // PostgreSQL : RETURNING id pour récupérer le dernier ID inséré
        if (self::isPgsql()) {
            $sql .= " RETURNING id";
            $stmt = self::getConn()->prepare($sql);
            $stmt->execute(array_values($data));
            $row = $stmt->fetch();
            return (int)($row['id'] ?? 0);
        }

        self::query($sql, array_values($data));
        return (int)self::getConn()->lastInsertId();
    }

    public static function update(string $table, array $data, string $where, array $whereParams = []): int {
        $set    = [];
        $values = [];
        foreach ($data as $col => $val) {
            $set[]    = "{$col} = ?";
            $values[] = $val;
        }
        $sql  = "UPDATE {$table} SET " . implode(', ', $set) . " WHERE {$where}";
        $stmt = self::query($sql, array_merge($values, $whereParams));
        return $stmt->rowCount();
    }

    public static function delete(string $table, string $where, array $whereParams = []): int {
        $sql  = "DELETE FROM {$table} WHERE {$where}";
        $stmt = self::query($sql, $whereParams);
        return $stmt->rowCount();
    }
}
