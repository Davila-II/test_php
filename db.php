<?php
require_once __DIR__ . '/models/Database.php';

try {
    $BD = Database::getConn();
} catch (Exception $e) {
    die('Erreur de connexion : ' . $e->getMessage());
}
