<?php
require_once __DIR__ . '/Database.php';

header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Veuillez saisir une adresse email valide.']);
        exit;
    }

    try {
        // Check if already subscribed
        $exists = Database::fetchOne("SELECT id FROM newsletter WHERE email = ?", [$email]);
        if (!$exists) {
            Database::insert('newsletter', ['email' => $email]);
        }

        echo json_encode([
            'status' => 'success',
            'message' => 'Merci pour votre inscription à la newsletter DjoKhouse SARL !'
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Une erreur est survenue : ' . $e->getMessage()
        ]);
    }
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Méthode non autorisée.']);
exit;
