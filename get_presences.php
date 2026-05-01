<?php
// get_presences.php — Récupère toutes les présences
require_once 'config.php';

try {
    $stmt = $pdo->query("
        SELECT
            id, nom, prenom, lieu, contact, email, phone,
            is_member, church_name, status, thank_you_sent,
            DATE_FORMAT(date, '%Y-%m-%dT%H:%i:%sZ') AS date
        FROM huperoon_church
        ORDER BY date DESC
    ");

    $rows = $stmt->fetchAll();
    foreach ($rows as &$r) {
        $r['is_member']      = (bool)$r['is_member'];
        $r['thank_you_sent'] = (bool)$r['thank_you_sent'];
    }

    echo json_encode($rows);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
