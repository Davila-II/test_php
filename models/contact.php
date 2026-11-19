<?php
require_once __DIR__ . '/Database.php';

header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? 'Demande générale');
    $message = trim($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($message)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Veuillez remplir tous les champs obligatoires (Nom, Email, Message).'
        ]);
        exit;
    }

    try {
        Database::insert('messages', [
            'name' => htmlspecialchars($name),
            'email' => filter_var($email, FILTER_SANITIZE_EMAIL),
            'phone' => htmlspecialchars($phone),
            'subject' => htmlspecialchars($subject),
            'message' => htmlspecialchars($message)
        ]);

        echo json_encode([
            'status' => 'success',
            'message' => 'Merci ' . htmlspecialchars($name) . ' ! Votre message a bien été transmis à DjoKhouse SCI. Nous vous répondrons dans les plus brefs délais.'
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Une erreur est survenue lors de l\'envoi : ' . $e->getMessage()
        ]);
    }
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Méthode non autorisée.']);
exit;
