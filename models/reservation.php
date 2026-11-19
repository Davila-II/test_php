<?php
require_once __DIR__ . '/Database.php';

header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $details = trim($_POST['details'] ?? $_POST['message'] ?? '');
    $property_id = !empty($_POST['property_id']) ? (int)$_POST['property_id'] : null;
    $service_id = !empty($_POST['service_id']) ? (int)$_POST['service_id'] : null;

    if (empty($name) || empty($phone)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Veuillez au minimum renseigner votre Nom et Numéro de téléphone.'
        ]);
        exit;
    }

    try {
        Database::insert('reservations', [
            'name' => htmlspecialchars($name),
            'email' => filter_var($email, FILTER_SANITIZE_EMAIL),
            'phone' => htmlspecialchars($phone),
            'property_id' => $property_id,
            'service_id' => $service_id,
            'details' => htmlspecialchars($details),
            'status' => 'En attente'
        ]);

        echo json_encode([
            'status' => 'success',
            'message' => 'Votre demande a été enregistrée avec succès ! Notre conseiller DjoKhouse SCI vous contactera sous 24h.'
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
