<?php
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Cache-Control: private");
    header("Pragma: no-cache");
    header('Content-Type:text/html; Charset=UTF-8');

    ob_start();

    // Auto-detect base URL based on environment
    if (!defined("BASEURL")) {
        $scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
        define("BASEURL", $scriptDir ? $scriptDir . '/' : '/');
    }

    require_once("models/SessionManager.php");
    require_once("db.php");
    require_once("models/i18n.php");
    require_once("fonctions.php");
    require_once("models/classes.php");

    // Démarrer la session (compatible Vercel)
    SessionManager::start();

    $query = array();
    $parts = parse_url($_SERVER["REQUEST_URI"] ?? '');
    if (isset($parts['query'])) {
        parse_str($parts['query'], $query);
        if (isset($query) && count($query) > 0) {
            foreach ($query as $key => $value) {
                $_REQUEST[$key] = $value;
            }
        }
    }

    $ctrl = "home"; // Page d'accueil par défaut
    if (!empty($_REQUEST['page'])) {
        $ctrl = strtolower(trim($_REQUEST['page']));
    } elseif (!empty($parts['path'])) {
        $cleanPath = trim($parts['path'], '/');
        // Strip subfolder or script name if present
        $cleanPath = preg_replace('#^(baronne/|index\.php/?)+#i', '', $cleanPath);
        $segments = explode('/', $cleanPath);
        if (!empty($segments[0])) {
            $ctrl = strtolower(trim($segments[0]));
        }
    }

    if (!SessionManager::has('lang')) {
        SessionManager::set('lang', 'fr');
    }

    // Dynamic global settings
    $site_settings = Database::fetchOne("SELECT * FROM site_settings WHERE id = 1");

    switch ($ctrl) {
        case 'admin': {
            include("views/admin.php");
            break;
        }

        case 'accueil':
        case 'home': {
            $page_title = "Accueil — " . ($site_settings['site_name'] ?? 'DjoKhouse SCI');
            include("views/header.php");
            include("views/menu.php");
            include("views/home.php");
            include("views/footer.php");
            break;
        }

        case 'about': {
            $page_title = "À Propos — " . ($site_settings['site_name'] ?? 'DjoKhouse SCI');
            include("views/header.php");
            include("views/menu.php");
            include("views/about.php");
            include("views/footer.php");
            break;
        }

        case 'portfolio': {
            $page_title = "Nos Projets & Terrains — " . ($site_settings['site_name'] ?? 'DjoKhouse SCI');
            include("views/header.php");
            include("views/menu.php");
            include("views/portfolio.php");
            include("views/footer.php");
            break;
        }

        case 'projetdetail': {
            $page_title = "Détail du projet — " . ($site_settings['site_name'] ?? 'DjoKhouse SCI');
            include("views/header.php");
            include("views/menu.php");
            include("views/projetdetail.php");
            include("views/footer.php");
            break;
        }

        case 'conseils': {
            $page_title = "Conseils Fonciers & Immobiliers";
            include("views/header.php");
            include("views/menu.php");
            include("views/conseils.php");
            include("views/footer.php");
            break;
        }

        case 'urbanisme': {
            $page_title = "Urbanisme & Aménagement";
            include("views/header.php");
            include("views/menu.php");
            include("views/urbanisme.php");
            include("views/footer.php");
            break;
        }

        case 'topographie': {
            $page_title = "Topographie & Génie Civil";
            include("views/header.php");
            include("views/menu.php");
            include("views/topographie.php");
            include("views/footer.php");
            break;
        }

        case 'bornage': {
            $page_title = "Bornage & Délimitation Foncière";
            include("views/header.php");
            include("views/menu.php");
            include("views/bornage.php");
            include("views/footer.php");
            break;
        }

        case 'prestation': {
            $page_title = "Prestation de Services";
            include("views/header.php");
            include("views/menu.php");
            include("views/prestation.php");
            include("views/footer.php");
            break;
        }

        case 'service': {
            $page_title = "Nos Services";
            include("views/header.php");
            include("views/menu.php");
            include("views/service.php");
            include("views/footer.php");
            break;
        }

        case 'contact': {
            if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
                include("models/contact.php");
                exit;
            }
            $page_title = "Contactez-Nous";
            include("views/header.php");
            include("views/menu.php");
            include("views/contact.php");
            include("views/footer.php");
            break;
        }

        case 'newsletter': {
            include("models/newsletter.php");
            exit;
        }

        case 'reservation': {
            include("models/reservation.php");
            exit;
        }

        case 'mail': {
            include("models/contact.php");
            exit;
        }

        case 'set_lang': {
            // Endpoint AJAX — sauvegarde la langue sans rechargement complet
            $newLang = strtolower(trim($_GET['lang'] ?? $_POST['lang'] ?? 'fr'));
            if (!in_array($newLang, ['fr', 'en'])) {
                $newLang = 'fr';
            }
            SessionManager::set('lang', $newLang);
            if (!headers_sent()) {
                setcookie('djok_lang', $newLang, time() + (86400 * 365), '/');
            }
            header('Content-Type: application/json');
            echo json_encode(['ok' => true, 'lang' => $newLang]);
            exit;
        }

        default: {
            $page_title = "Accueil — " . ($site_settings['site_name'] ?? 'DjoKhouse SCI');
            include("views/header.php");
            include("views/menu.php");
            include("views/home.php");
            include("views/footer.php");
            break;
        }
    }

    ob_end_flush();
?>
