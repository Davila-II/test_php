<?php
/**
 * Back-office d'administration DjoKhouse SARL
 */
require_once __DIR__ . '/../models/Database.php';
require_once __DIR__ . '/../models/SessionManager.php';
require_once __DIR__ . '/../models/SupabaseStorage.php';
require_once __DIR__ . '/../models/i18n.php';

// Session setup (compatible Vercel via SessionManager)
SessionManager::start();

$db = Database::getConn();
$authError = '';
$actionSuccess = '';

// Handle Login
if (isset($_POST['action']) && $_POST['action'] === 'login') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $user = Database::fetchOne("SELECT * FROM admin_users WHERE username = ?", [$username]);
    if ($user && password_verify($password, $user['password_hash'])) {
        SessionManager::set('admin_logged', true);
        SessionManager::set('admin_user', $user['username']);
        header("Location: index.php?page=admin");
        exit;
    } else {
        $authError = "Identifiants incorrects.";
    }
}

// Handle Logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    SessionManager::remove('admin_logged');
    SessionManager::remove('admin_user');
    header("Location: index.php?page=admin");
    exit;
}

// Check auth
$isLoggedIn = SessionManager::get('admin_logged') === true;

// If logged in, handle admin actions
if ($isLoggedIn && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Add or Edit Property
    if ($action === 'save_property') {
        $id          = !empty($_POST['id']) ? (int)$_POST['id'] : null;
        $title       = trim($_POST['title'] ?? '');
        $category    = trim($_POST['category'] ?? 'Terrain');
        $type        = trim($_POST['type'] ?? 'Vente');
        $price       = trim($_POST['price'] ?? '');
        $surface     = trim($_POST['surface'] ?? '');
        $location    = trim($_POST['location'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        $image       = trim($_POST['existing_image'] ?? 'images/folio/1.jpg');

        // Handle File Upload via SupabaseStorage (ou local)
        if (!empty($_FILES['image_file']['name']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                try {
                    $image = SupabaseStorage::upload($_FILES['image_file'], 'properties');
                } catch (RuntimeException $e) {
                    $actionSuccess = 'Avertissement upload : ' . $e->getMessage();
                }
            }
        }

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));

        if ($id) {
            Database::update('properties', [
                'title'       => $title,
                'slug'        => $slug,
                'category'    => $category,
                'type'        => $type,
                'price'       => $price,
                'surface'     => $surface,
                'location'    => $location,
                'description' => $description,
                'image'       => $image,
                'is_featured' => $is_featured
            ], 'id = ?', [$id]);
            $actionSuccess = "Bien / Terrain modifié avec succès !";
        } else {
            Database::insert('properties', [
                'title'       => $title,
                'slug'        => $slug,
                'category'    => $category,
                'type'        => $type,
                'price'       => $price,
                'surface'     => $surface,
                'location'    => $location,
                'description' => $description,
                'image'       => $image,
                'is_featured' => $is_featured
            ]);
            $actionSuccess = "Nouveau bien / terrain ajouté avec succès !";
        }
    }

    // Delete Property
    if ($action === 'delete_property') {
        $id = (int)$_POST['id'];
        Database::delete('properties', 'id = ?', [$id]);
        $actionSuccess = "Bien / Terrain supprimé.";
    }

    // Update Service
    if ($action === 'update_service') {
        $id      = (int)$_POST['id'];
        $title   = trim($_POST['title'] ?? '');
        $summary = trim($_POST['summary'] ?? '');
        $content = trim($_POST['content'] ?? '');

        Database::update('services', [
            'title'   => $title,
            'summary' => $summary,
            'content' => $content
        ], 'id = ?', [$id]);
        $actionSuccess = "Service mis à jour avec succès !";
    }

    // Update Message Status
    if ($action === 'mark_message_read') {
        $id = (int)$_POST['id'];
        Database::update('messages', ['is_read' => 1], 'id = ?', [$id]);
        $actionSuccess = "Message marqué comme lu.";
    }

    if ($action === 'delete_message') {
        $id = (int)$_POST['id'];
        Database::delete('messages', 'id = ?', [$id]);
        $actionSuccess = "Message supprimé.";
    }

    // Update Reservation Status
    if ($action === 'update_reservation_status') {
        $id     = (int)$_POST['id'];
        $status = trim($_POST['status'] ?? 'En attente');
        Database::update('reservations', ['status' => $status], 'id = ?', [$id]);
        $actionSuccess = "Statut de la demande mis à jour ($status).";
    }

    if ($action === 'delete_reservation') {
        $id = (int)$_POST['id'];
        Database::delete('reservations', 'id = ?', [$id]);
        $actionSuccess = "Demande supprimée.";
    }

    // Update Site Settings
    if ($action === 'save_settings') {
        $site_name     = trim($_POST['site_name'] ?? 'DjoKhouse SARL');
        $slogan        = trim($_POST['slogan'] ?? '');
        $phone         = trim($_POST['phone'] ?? '');
        $phone2        = trim($_POST['phone2'] ?? '');
        $email         = trim($_POST['email'] ?? '');
        $address       = trim($_POST['address'] ?? '');
        $about_summary = trim($_POST['about_summary'] ?? '');
        $facebook      = trim($_POST['facebook'] ?? '');
        $linkedin      = trim($_POST['linkedin'] ?? '');
        $whatsapp      = trim($_POST['whatsapp'] ?? '');

        Database::update('site_settings', [
            'site_name'     => $site_name,
            'slogan'        => $slogan,
            'phone'         => $phone,
            'phone2'        => $phone2,
            'email'         => $email,
            'address'       => $address,
            'about_summary' => $about_summary,
            'facebook'      => $facebook,
            'linkedin'      => $linkedin,
            'whatsapp'      => $whatsapp
        ], 'id = 1');

        // Optional change password
        $new_pass = trim($_POST['new_password'] ?? '');
        if (!empty($new_pass)) {
            $hash = password_hash($new_pass, PASSWORD_DEFAULT);
            Database::update('admin_users', ['password_hash' => $hash], 'username = ?', [SessionManager::get('admin_user')]);
            $actionSuccess = "Paramètres et mot de passe enregistrés avec succès !";
        } else {
            $actionSuccess = "Paramètres du site enregistrés avec succès !";
        }
    }
}

// Fetch dashboard data
if ($isLoggedIn) {
    $settings     = Database::fetchOne("SELECT * FROM site_settings WHERE id = 1");
    $properties   = Database::fetchAll("SELECT * FROM properties ORDER BY id DESC");
    $services     = Database::fetchAll("SELECT * FROM services ORDER BY order_num ASC");
    $messages     = Database::fetchAll("SELECT * FROM messages ORDER BY id DESC");
    $reservations = Database::fetchAll("SELECT * FROM reservations ORDER BY id DESC");
    $newsletters  = Database::fetchAll("SELECT * FROM newsletter ORDER BY id DESC");

    $unreadCount     = Database::query("SELECT COUNT(*) FROM messages WHERE is_read = 0")->fetchColumn();
    $pendingResCount = Database::query("SELECT COUNT(*) FROM reservations WHERE status = 'En attente'")->fetchColumn();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration — DjoKhouse SARL</title>
    <link rel="shortcut icon" href="images/favicon.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #008751;
            --primary-hover: #006b3f;
            --red: #E01E26;
            --yellow: #F5B800;
            --dark: #1f2937;
            --light: #f9fafb;
            --border: #e5e7eb;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; }
        body { background: #f3f4f6; color: #374151; min-height: 100vh; display: flex; flex-direction: column; }
        a { text-decoration: none; color: inherit; }

        /* Login Box */
        .login-wrap { max-width: 420px; width: 90%; margin: 80px auto; background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.06); text-align: center; }
        .login-wrap img { max-height: 80px; margin-bottom: 20px; }
        .login-wrap h2 { font-size: 22px; color: #111; margin-bottom: 8px; }
        .login-wrap p { font-size: 14px; color: #6b7280; margin-bottom: 24px; }
        .form-group { text-align: left; margin-bottom: 18px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }
        .form-control { width: 100%; padding: 11px 14px; border: 1px solid var(--border); border-radius: 8px; font-size: 14px; transition: border 0.2s; }
        .form-control:focus { outline: none; border-color: var(--primary); }
        .btn-submit { width: 100%; background: var(--primary); color: #fff; padding: 12px; border: none; border-radius: 8px; font-size: 15px; font-weight: 600; cursor: pointer; transition: background 0.2s; }
        .btn-submit:hover { background: var(--primary-hover); }
        .alert-error { background: #fee2e2; color: #b91c1c; padding: 12px; border-radius: 8px; font-size: 14px; margin-bottom: 18px; }
        .alert-success { background: #dcfce7; color: #15803d; padding: 12px 18px; border-radius: 8px; font-size: 14px; margin-bottom: 24px; }

        /* Dashboard Layout */
        .admin-header { background: #fff; border-bottom: 1px solid var(--border); padding: 14px 28px; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 50; }
        .admin-header .brand { display: flex; align-items: center; gap: 14px; }
        .admin-header .brand img { height: 45px; }
        .admin-header .brand h1 { font-size: 18px; color: #111; font-weight: 700; }
        .admin-header .nav-actions { display: flex; align-items: center; gap: 16px; font-size: 14px; }
        .admin-header .btn-site { background: #f3f4f6; padding: 8px 14px; border-radius: 6px; font-weight: 600; color: #374151; }
        .admin-header .btn-logout { background: #fee2e2; color: #b91c1c; padding: 8px 14px; border-radius: 6px; font-weight: 600; }

        .admin-container { max-width: 1300px; width: 95%; margin: 28px auto; flex: 1; }

        /* Stat Cards */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 18px; margin-bottom: 28px; }
        .stat-card { background: #fff; padding: 22px; border-radius: 10px; border: 1px solid var(--border); display: flex; align-items: center; gap: 18px; }
        .stat-icon { width: 52px; height: 52px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 22px; }
        .stat-icon.green { background: #dcfce7; color: var(--primary); }
        .stat-icon.red { background: #fee2e2; color: var(--red); }
        .stat-icon.yellow { background: #fef9c3; color: #b45309; }
        .stat-icon.blue { background: #e0f2fe; color: #0284c7; }
        .stat-info h3 { font-size: 24px; font-weight: 700; color: #111; }
        .stat-info p { font-size: 13px; color: #6b7280; font-weight: 500; }

        /* Tabs */
        .tabs-nav { display: flex; gap: 8px; border-bottom: 1px solid var(--border); margin-bottom: 24px; overflow-x: auto; }
        .tab-btn { background: none; border: none; padding: 12px 20px; font-size: 14px; font-weight: 600; color: #6b7280; cursor: pointer; border-bottom: 2px solid transparent; transition: all 0.2s; white-space: nowrap; }
        .tab-btn.active { color: var(--primary); border-bottom-color: var(--primary); }
        .tab-btn .badge { background: var(--red); color: #fff; font-size: 11px; padding: 2px 7px; border-radius: 12px; margin-left: 6px; }

        .tab-content { display: none; }
        .tab-content.active { display: block; }

        /* Card Content */
        .admin-card { background: #fff; border-radius: 10px; border: 1px solid var(--border); padding: 24px; margin-bottom: 28px; }
        .admin-card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .admin-card-header h2 { font-size: 18px; color: #111; font-weight: 700; }
        .btn-add { background: var(--primary); color: #fff; padding: 9px 16px; border-radius: 6px; font-size: 14px; font-weight: 600; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; }
        .btn-add:hover { background: var(--primary-hover); }

        /* Tables */
        .table-responsive { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; text-align: left; font-size: 14px; }
        th { background: #f9fafb; padding: 12px 16px; color: #4b5563; font-weight: 600; border-bottom: 1px solid var(--border); }
        td { padding: 14px 16px; border-bottom: 1px solid var(--border); vertical-align: middle; }
        tr:hover td { background: #fafafa; }
        .img-thumb { width: 60px; height: 45px; object-fit: cover; border-radius: 6px; }
        .tag { display: inline-block; padding: 3px 9px; border-radius: 12px; font-size: 12px; font-weight: 600; }
        .tag-green { background: #dcfce7; color: #15803d; }
        .tag-blue { background: #e0f2fe; color: #0369a1; }
        .tag-yellow { background: #fef9c3; color: #a16207; }

        .actions-btns { display: flex; gap: 8px; }
        .btn-sm { padding: 6px 10px; border-radius: 5px; font-size: 12px; font-weight: 600; border: none; cursor: pointer; }
        .btn-edit { background: #e0f2fe; color: #0284c7; }
        .btn-del { background: #fee2e2; color: #b91c1c; }

        /* Modal */
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 100; align-items: center; justify-content: center; }
        .modal.active { display: flex; }
        .modal-box { background: #fff; border-radius: 12px; width: 90%; max-width: 650px; max-height: 90vh; overflow-y: auto; padding: 30px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid var(--border); padding-bottom: 12px; }
        .modal-header h3 { font-size: 18px; color: #111; }
        .close-modal { font-size: 22px; cursor: pointer; color: #9ca3af; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

        /* Language Switcher — Admin */
        .djok-lang-switcher {
            display: inline-flex;
            align-items: center;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            padding: 3px 6px;
            font-weight: 700;
            font-size: 11px;
            letter-spacing: 0.5px;
            user-select: none;
        }
        .djok-lang-switcher .lang-btn {
            display: inline-block;
            padding: 4px 9px;
            border-radius: 14px;
            color: #6b7280;
            text-decoration: none;
            transition: all 0.18s ease;
        }
        .djok-lang-switcher .lang-btn:hover { color: #1f2937; }
        .djok-lang-switcher .lang-btn.active {
            background: #1f2937 !important;
            color: #ffffff !important;
        }
        .djok-lang-switcher .lang-sep { color: #d1d5db; margin: 0 2px; font-size: 10px; }
    </style>
</head>
<body>

<?php if (!$isLoggedIn): ?>
    <!-- Login Screen -->
    <div class="login-wrap">
        <img src="images/logo.png" alt="DjoKhouse SARL">
        <div style="margin: 12px auto;"><?= i18n::renderSwitcher() ?></div>
        <h2><?= __("admin_title") ?></h2>
        <p><?= __("admin_sub") ?></p>

        <?php if ($authError): ?>
            <div class="alert-error"><?= htmlspecialchars($authError) ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="action" value="login">
            <div class="form-group">
                <label><?= __("admin_username") ?></label>
                <input type="text" name="username" class="form-control" value="admin" required autofocus>
            </div>
            <div class="form-group">
                <label><?= __("admin_password") ?></label>
                <input type="password" name="password" class="form-control" value="admin123" required>
            </div>
            <button type="submit" class="btn-submit"><i class="fa fa-lock"></i> <?= __("admin_login_btn") ?></button>
        </form>
    </div>

<?php else: ?>
    <!-- Top Bar -->
    <header class="admin-header">
        <div class="brand">
            <img src="images/logo.png" alt="Logo">
            <h1>DjoKhouse SARL &bull; <?= __("admin_title") ?></h1>
        </div>
        <div class="nav-actions">
            <?= i18n::renderSwitcher() ?>
            <span><?= __("admin_logged_as") ?> <strong><?= htmlspecialchars(SessionManager::get('admin_user')) ?></strong></span>
            <a href="home" target="_blank" class="btn-site"><i class="fa fa-globe"></i> <?= __("admin_view_site") ?></a>
            <a href="index.php?page=admin&action=logout" class="btn-logout"><i class="fa fa-sign-out-alt"></i> <?= __("admin_logout") ?></a>
        </div>
    </header>

    <div class="admin-container">

        <?php if ($actionSuccess): ?>
            <div class="alert-success"><i class="fa fa-check-circle"></i> <?= htmlspecialchars($actionSuccess) ?></div>
        <?php endif; ?>

        <!-- Stat Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon green"><i class="fa fa-map-marked-alt"></i></div>
                <div class="stat-info">
                    <h3><?= count($properties) ?></h3>
                    <p><?= __("admin_stat_props") ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue"><i class="fa fa-concierge-bell"></i></div>
                <div class="stat-info">
                    <h3><?= count($services) ?></h3>
                    <p><?= __("admin_stat_services") ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon red">
                    <i class="fa fa-envelope"></i>
                </div>
                <div class="stat-info">
                    <h3><?= count($messages) ?></h3>
                    <p><?= __("admin_stat_messages") ?> <?php if ($unreadCount > 0): ?><span class="tag tag-yellow"><?= $unreadCount ?> nouveau<?= $unreadCount > 1 ? 'x' : '' ?></span><?php endif; ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon yellow">
                    <i class="fa fa-calendar-check"></i>
                </div>
                <div class="stat-info">
                    <h3><?= count($reservations) ?></h3>
                    <p><?= __("admin_stat_devis") ?></p>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="tabs-nav">
            <button class="tab-btn active" onclick="switchTab('properties')"><i class="fa fa-home"></i> <?= __("admin_tab_props") ?></button>
            <button class="tab-btn" onclick="switchTab('messages')"><i class="fa fa-envelope"></i> <?= __("admin_tab_messages") ?> <?php if ($unreadCount > 0): ?><span class="badge"><?= $unreadCount ?></span><?php endif; ?></button>
            <button class="tab-btn" onclick="switchTab('reservations')"><i class="fa fa-file-invoice"></i> <?= __("admin_tab_reservations") ?> <?php if ($pendingResCount > 0): ?><span class="badge"><?= $pendingResCount ?></span><?php endif; ?></button>
            <button class="tab-btn" onclick="switchTab('services')"><i class="fa fa-briefcase"></i> <?= __("admin_tab_services") ?></button>
            <button class="tab-btn" onclick="switchTab('newsletter')"><i class="fa fa-users"></i> <?= __("admin_tab_news") ?> (<?= count($newsletters) ?>)</button>
            <button class="tab-btn" onclick="switchTab('settings')"><i class="fa fa-cog"></i> <?= __("admin_tab_settings") ?></button>
        </div>

        <!-- TAB 1: PROPERTIES / TERRAINS -->
        <div id="tab-properties" class="tab-content active">
            <div class="admin-card">
                <div class="admin-card-header">
                    <h2><?= __("admin_tab_props") ?></h2>
                    <button class="btn-add" onclick="openPropertyModal()"><i class="fa fa-plus"></i> <?= __("admin_add_prop") ?></button>
                </div>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th><?= __("admin_col_photo") ?></th>
                                <th><?= __("admin_col_title") ?></th>
                                <th><?= __("admin_col_category") ?></th>
                                <th><?= __("admin_col_price") ?></th>
                                <th><?= __("admin_col_surface") ?></th>
                                <th><?= __("admin_col_location") ?></th>
                                <th><?= __("admin_col_type") ?></th>
                                <th><?= __("admin_col_actions") ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($properties as $p): ?>
                                <tr>
                                    <td><img src="<?= htmlspecialchars($p['image']) ?>" class="img-thumb" alt=""></td>
                                    <td><strong><?= htmlspecialchars($p['title']) ?></strong></td>
                                    <td><span class="tag tag-green"><?= htmlspecialchars($p['category']) ?></span></td>
                                    <td><?= htmlspecialchars($p['price']) ?></td>
                                    <td><?= htmlspecialchars($p['surface']) ?></td>
                                    <td><?= htmlspecialchars($p['location']) ?></td>
                                    <td><span class="tag tag-blue"><?= htmlspecialchars($p['type']) ?></span></td>
                                    <td>
                                        <div class="actions-btns">
                                            <button class="btn-sm btn-edit" onclick='editProperty(<?= json_encode($p) ?>)'><i class="fa fa-pencil"></i></button>
                                            <form method="POST" onsubmit="return confirm('Confirmer la suppression de ce bien ?');" style="display:inline;">
                                                <input type="hidden" name="action" value="delete_property">
                                                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                                <button type="submit" class="btn-sm btn-del"><i class="fa fa-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB 2: MESSAGES -->
        <div id="tab-messages" class="tab-content">
            <div class="admin-card">
                <div class="admin-card-header">
                    <h2>Messages du formulaire de contact</h2>
                </div>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Statut</th>
                                <th>Date</th>
                                <th>Nom</th>
                                <th>Coordonnées</th>
                                <th>Sujet</th>
                                <th>Message</th>
                                <th><?= __("admin_col_actions") ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($messages)): ?>
                                <tr><td colspan="7" style="text-align:center; color:#9ca3af;">Aucun message reçu pour le moment.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($messages as $m): ?>
                                <tr>
                                    <td>
                                        <?php if ($m['is_read']): ?>
                                            <span class="tag tag-blue">Lu</span>
                                        <?php else: ?>
                                            <span class="tag tag-yellow">Nouveau</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($m['created_at'])) ?></td>
                                    <td><strong><?= htmlspecialchars($m['name']) ?></strong></td>
                                    <td>
                                        <a href="mailto:<?= htmlspecialchars($m['email']) ?>"><?= htmlspecialchars($m['email']) ?></a><br>
                                        <small><?= htmlspecialchars($m['phone']) ?></small>
                                    </td>
                                    <td><?= htmlspecialchars($m['subject']) ?></td>
                                    <td style="max-width:320px; white-space:pre-wrap; font-size:13px;"><?= htmlspecialchars($m['message']) ?></td>
                                    <td>
                                        <div class="actions-btns">
                                            <?php if (!$m['is_read']): ?>
                                                <form method="POST">
                                                    <input type="hidden" name="action" value="mark_message_read">
                                                    <input type="hidden" name="id" value="<?= $m['id'] ?>">
                                                    <button type="submit" class="btn-sm btn-edit" title="Marquer comme lu"><i class="fa fa-check"></i></button>
                                                </form>
                                            <?php endif; ?>
                                            <form method="POST" onsubmit="return confirm('Supprimer ce message ?');">
                                                <input type="hidden" name="action" value="delete_message">
                                                <input type="hidden" name="id" value="<?= $m['id'] ?>">
                                                <button type="submit" class="btn-sm btn-del" title="Supprimer"><i class="fa fa-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB 3: RESERVATIONS / DEVIS -->
        <div id="tab-reservations" class="tab-content">
            <div class="admin-card">
                <div class="admin-card-header">
                    <h2>Demandes de devis & Réservations</h2>
                </div>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Client</th>
                                <th>Téléphone</th>
                                <th>Email</th>
                                <th>Détails</th>
                                <th>Statut</th>
                                <th><?= __("admin_col_actions") ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($reservations)): ?>
                                <tr><td colspan="7" style="text-align:center; color:#9ca3af;">Aucune demande de devis enregistrée.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($reservations as $r): ?>
                                <tr>
                                    <td><?= date('d/m/Y H:i', strtotime($r['created_at'])) ?></td>
                                    <td><strong><?= htmlspecialchars($r['name']) ?></strong></td>
                                    <td><a href="tel:<?= htmlspecialchars($r['phone']) ?>"><?= htmlspecialchars($r['phone']) ?></a></td>
                                    <td><?= htmlspecialchars($r['email']) ?></td>
                                    <td style="max-width:300px;"><?= htmlspecialchars($r['details']) ?></td>
                                    <td>
                                        <span class="tag <?= $r['status'] === 'Traité' ? 'tag-green' : ($r['status'] === 'En attente' ? 'tag-yellow' : 'tag-blue') ?>">
                                            <?= htmlspecialchars($r['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="actions-btns">
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="action" value="update_reservation_status">
                                                <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                                <select name="status" onchange="this.form.submit()" class="form-control" style="padding:4px 8px; font-size:12px;">
                                                    <option value="En attente" <?= $r['status'] === 'En attente' ? 'selected' : '' ?>>En attente</option>
                                                    <option value="Contacté" <?= $r['status'] === 'Contacté' ? 'selected' : '' ?>>Contacté</option>
                                                    <option value="Traité" <?= $r['status'] === 'Traité' ? 'selected' : '' ?>>Traité</option>
                                                    <option value="Annulé" <?= $r['status'] === 'Annulé' ? 'selected' : '' ?>>Annulé</option>
                                                </select>
                                            </form>
                                            <form method="POST" onsubmit="return confirm('Supprimer cette demande ?');" style="display:inline;">
                                                <input type="hidden" name="action" value="delete_reservation">
                                                <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                                <button type="submit" class="btn-sm btn-del"><i class="fa fa-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB 4: SERVICES -->
        <div id="tab-services" class="tab-content">
            <div class="admin-card">
                <div class="admin-card-header">
                    <h2>Services Proposés</h2>
                </div>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Slug</th>
                                <th><?= __("admin_col_title") ?></th>
                                <th>Résumé</th>
                                <th><?= __("admin_col_actions") ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($services as $s): ?>
                                <tr>
                                    <td><code><?= htmlspecialchars($s['slug']) ?></code></td>
                                    <td><strong><?= htmlspecialchars($s['title']) ?></strong></td>
                                    <td style="max-width:400px;"><?= htmlspecialchars($s['summary']) ?></td>
                                    <td>
                                        <button class="btn-sm btn-edit" onclick='editService(<?= json_encode($s) ?>)'><i class="fa fa-pencil"></i> Modifier</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB 5: NEWSLETTER -->
        <div id="tab-newsletter" class="tab-content">
            <div class="admin-card">
                <div class="admin-card-header">
                    <h2>Liste des abonnés à la Newsletter</h2>
                </div>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Adresse Email</th>
                                <th>Date d'inscription</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($newsletters)): ?>
                                <tr><td colspan="3" style="text-align:center; color:#9ca3af;">Aucun abonné pour le moment.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($newsletters as $idx => $nl): ?>
                                <tr>
                                    <td><?= $idx + 1 ?></td>
                                    <td><strong><?= htmlspecialchars($nl['email']) ?></strong></td>
                                    <td><?= date('d/m/Y H:i', strtotime($nl['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB 6: SETTINGS -->
        <div id="tab-settings" class="tab-content">
            <div class="admin-card">
                <div class="admin-card-header">
                    <h2>Paramètres Généraux du Site</h2>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="save_settings">
                    <div class="grid-2">
                        <div class="form-group">
                            <label>Nom de l'entreprise</label>
                            <input type="text" name="site_name" class="form-control" value="<?= htmlspecialchars($settings['site_name']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Slogan officiel</label>
                            <input type="text" name="slogan" class="form-control" value="<?= htmlspecialchars($settings['slogan']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Téléphone principal</label>
                            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($settings['phone']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Téléphone secondaire (WhatsApp)</label>
                            <input type="text" name="phone2" class="form-control" value="<?= htmlspecialchars($settings['phone2'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Email de contact officiel</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($settings['email']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Adresse physique</label>
                            <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($settings['address']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Lien Facebook</label>
                            <input type="text" name="facebook" class="form-control" value="<?= htmlspecialchars($settings['facebook'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Lien LinkedIn</label>
                            <input type="text" name="linkedin" class="form-control" value="<?= htmlspecialchars($settings['linkedin'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Texte de présentation générale</label>
                        <textarea name="about_summary" class="form-control" rows="3"><?= htmlspecialchars($settings['about_summary'] ?? '') ?></textarea>
                    </div>

                    <hr style="margin:24px 0; border:0; border-top:1px solid var(--border);">
                    <h3 style="font-size:16px; margin-bottom:12px; color:#111;">Sécurité du compte</h3>
                    <div class="form-group" style="max-width:350px;">
                        <label>Nouveau mot de passe administrateur (laisser vide pour ne pas changer)</label>
                        <input type="password" name="new_password" class="form-control" placeholder="••••••••">
                    </div>

                    <button type="submit" class="btn-add"><i class="fa fa-save"></i> Enregistrer les modifications</button>
                </form>
            </div>
        </div>

    </div>

    <!-- MODAL: ADD / EDIT PROPERTY -->
    <div id="propertyModal" class="modal">
        <div class="modal-box">
            <div class="modal-header">
                <h3 id="propModalTitle">Ajouter un bien / terrain</h3>
                <span class="close-modal" onclick="closePropertyModal()">&times;</span>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="save_property">
                <input type="hidden" name="id" id="propId" value="">
                <input type="hidden" name="existing_image" id="propExistingImage" value="images/folio/1.jpg">

                <div class="form-group">
                    <label>Titre de l'annonce *</label>
                    <input type="text" name="title" id="propTitle" class="form-control" placeholder="ex: Terrains PK14 Douala" required>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Catégorie</label>
                        <select name="category" id="propCategory" class="form-control">
                            <option value="Terrain">Terrain</option>
                            <option value="Urbanisme">Urbanisme</option>
                            <option value="Topographie">Topographie</option>
                            <option value="Bornage">Bornage</option>
                            <option value="Maison">Maison / Villa</option>
                            <option value="Prestation">Prestation</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Type / Statut</label>
                        <select name="type" id="propType" class="form-control">
                            <option value="Vente">À Vendre</option>
                            <option value="En cours">Projet en cours</option>
                            <option value="Projet réalisé">Projet réalisé</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Prix</label>
                        <input type="text" name="price" id="propPrice" class="form-control" placeholder="ex: 15 000 000 FCFA">
                    </div>
                    <div class="form-group">
                        <label>Superficie</label>
                        <input type="text" name="surface" id="propSurface" class="form-control" placeholder="ex: 500 m²">
                    </div>
                </div>

                <div class="form-group">
                    <label>Localisation</label>
                    <input type="text" name="location" id="propLocation" class="form-control" placeholder="ex: Douala, Cameroun">
                </div>

                <div class="form-group">
                    <label>Description détaillée</label>
                    <textarea name="description" id="propDescription" class="form-control" rows="4" placeholder="Détails du terrain, titre foncier, accès, viabilisation..."></textarea>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Téléverser une nouvelle photo</label>
                        <input type="file" name="image_file" class="form-control" accept="image/*">
                    </div>
                    <div class="form-group" style="display:flex; align-items:center; gap:10px; margin-top:24px;">
                        <input type="checkbox" name="is_featured" id="propFeatured" value="1" style="width:18px; height:18px;">
                        <label for="propFeatured" style="margin:0; cursor:pointer;">Mettre en avant sur la page d'accueil</label>
                    </div>
                </div>

                <div style="margin-top:20px; display:flex; justify-content:flex-end; gap:12px;">
                    <button type="button" class="btn-site" onclick="closePropertyModal()">Annuler</button>
                    <button type="submit" class="btn-add"><i class="fa fa-save"></i> Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: EDIT SERVICE -->
    <div id="serviceModal" class="modal">
        <div class="modal-box">
            <div class="modal-header">
                <h3>Modifier le service</h3>
                <span class="close-modal" onclick="closeServiceModal()">&times;</span>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="update_service">
                <input type="hidden" name="id" id="servId" value="">

                <div class="form-group">
                    <label>Titre du service</label>
                    <input type="text" name="title" id="servTitle" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Résumé court</label>
                    <textarea name="summary" id="servSummary" class="form-control" rows="2" required></textarea>
                </div>
                <div class="form-group">
                    <label>Description complète</label>
                    <textarea name="content" id="servContent" class="form-control" rows="4"></textarea>
                </div>

                <div style="margin-top:20px; display:flex; justify-content:flex-end; gap:12px;">
                    <button type="button" class="btn-site" onclick="closeServiceModal()">Annuler</button>
                    <button type="submit" class="btn-add"><i class="fa fa-save"></i> Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function switchTab(name) {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            event.currentTarget.classList.add('active');
            document.getElementById('tab-' + name).classList.add('active');
        }

        function openPropertyModal() {
            document.getElementById('propModalTitle').innerText = 'Ajouter un bien / terrain';
            document.getElementById('propId').value = '';
            document.getElementById('propTitle').value = '';
            document.getElementById('propPrice').value = '';
            document.getElementById('propSurface').value = '';
            document.getElementById('propLocation').value = '';
            document.getElementById('propDescription').value = '';
            document.getElementById('propFeatured').checked = false;
            document.getElementById('propertyModal').classList.add('active');
        }

        function editProperty(p) {
            document.getElementById('propModalTitle').innerText = 'Modifier le bien : ' + p.title;
            document.getElementById('propId').value = p.id;
            document.getElementById('propTitle').value = p.title;
            document.getElementById('propCategory').value = p.category;
            document.getElementById('propType').value = p.type;
            document.getElementById('propPrice').value = p.price;
            document.getElementById('propSurface').value = p.surface;
            document.getElementById('propLocation').value = p.location;
            document.getElementById('propDescription').value = p.description;
            document.getElementById('propExistingImage').value = p.image;
            document.getElementById('propFeatured').checked = (p.is_featured == 1);
            document.getElementById('propertyModal').classList.add('active');
        }

        function closePropertyModal() {
            document.getElementById('propertyModal').classList.remove('active');
        }

        function editService(s) {
            document.getElementById('servId').value = s.id;
            document.getElementById('servTitle').value = s.title;
            document.getElementById('servSummary').value = s.summary;
            document.getElementById('servContent').value = s.content;
            document.getElementById('serviceModal').classList.add('active');
        }

        function closeServiceModal() {
            document.getElementById('serviceModal').classList.remove('active');
        }
    </script>
<?php endif; ?>

<!-- ===== DjoKhouse — Language Switcher Admin (sans rechargement) ===== -->
<script>
(function () {
    var CURRENT_LANG = '<?= i18n::lang() ?>';

    function switchLangAdmin(lang) {
        if (lang === CURRENT_LANG) return;

        // Mise à jour visuelle immédiate
        document.querySelectorAll('.djok-switch-lang').forEach(function (btn) {
            btn.classList.toggle('active', btn.dataset.lang === lang);
        });

        // Sauvegarde session via AJAX puis rechargement de la page admin
        fetch('index.php?page=set_lang&lang=' + lang)
            .then(function () {
                CURRENT_LANG = lang;
                // Rechargement léger : on garde la même URL admin
                var url = new URL(window.location.href);
                url.searchParams.set('lang', lang);
                window.location.replace(url.toString());
            })
            .catch(function () {
                window.location.href = 'index.php?page=admin&lang=' + lang;
            });
    }

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.djok-switch-lang');
        if (!btn) return;
        e.preventDefault();
        switchLangAdmin(btn.dataset.lang);
    });
})();
</script>

</body>
</html>
