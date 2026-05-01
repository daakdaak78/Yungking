<?php
// delete.php — Supprimer une présence
require_once 'config.php';

$id = 0;
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    parse_str(file_get_contents('php://input'), $vars);
    $id = (int)($vars['id'] ?? 0);
} else {
    $id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
}

if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'ID invalide']);
    exit();
}

try {
    $check = $pdo->prepare("SELECT id FROM huperoon_church WHERE id = :id");
    $check->execute([':id' => $id]);
    if ($check->rowCount() === 0) {
        echo json_encode(['success' => false, 'error' => 'Enregistrement introuvable']);
        exit();
    }

    $pdo->prepare("DELETE FROM huperoon_church WHERE id = :id")->execute([':id' => $id]);

    echo json_encode(['success' => true, 'message' => 'Supprimé avec succès']);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
