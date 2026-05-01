<?php
// update_email_statut.php — Marquer l'email comme envoyé
require_once 'config.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || empty($data['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'ID requis']);
    exit();
}

try {
    $pdo->prepare("
        UPDATE huperoon_church
        SET thank_you_sent = 1, thank_you_sent_date = NOW()
        WHERE id = :id
    ")->execute([':id' => $data['id']]);

    $pdo->prepare("
        INSERT INTO email_logs (presence_id, email_to, status, sent_at)
        VALUES (:id, :email, 'sent', NOW())
    ")->execute([':id' => $data['id'], ':email' => $data['email'] ?? null]);

    echo json_encode(['success' => true, 'message' => 'Statut mis à jour']);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
