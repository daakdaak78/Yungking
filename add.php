<?php
// add.php — Ajouter une présence
require_once 'config.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || empty($data['nom']) || empty($data['prenom'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Nom et prénom requis']);
    exit();
}

try {
    $contact  = $data['contact'] ?? null;
    $parsed   = parseContact($contact);
    $isMember = in_array($data['is_member'] ?? false, [true, 1, 'true', '1'], true);

    $stmt = $pdo->prepare("
        INSERT INTO huperoon_church
            (nom, prenom, lieu, contact, email, phone, is_member, church_name, status, thank_you_sent)
        VALUES
            (:nom, :prenom, :lieu, :contact, :email, :phone, :is_member, :church_name, :status, 0)
    ");

    $stmt->execute([
        ':nom'         => trim($data['nom']),
        ':prenom'      => trim($data['prenom']),
        ':lieu'        => $data['lieu']        ?? null,
        ':contact'     => $contact,
        ':email'       => $parsed['email'],
        ':phone'       => $parsed['phone'],
        ':is_member'   => $isMember ? 1 : 0,
        ':church_name' => $data['church_name'] ?? ($isMember ? 'Huperoon-Church' : null),
        ':status'      => $data['status']      ?? 'present',
    ]);

    $newId = $pdo->lastInsertId();

    $s = $pdo->prepare("SELECT * FROM huperoon_church WHERE id = :id");
    $s->execute([':id' => $newId]);
    $record = $s->fetch();
    $record['is_member']      = (bool)$record['is_member'];
    $record['thank_you_sent'] = (bool)$record['thank_you_sent'];

    echo json_encode([
        'success' => true,
        'id'      => $newId,
        'data'    => $record,
        'message' => $parsed['email'] ? 'Enregistré – email de remerciement programmé' : 'Enregistré avec succès'
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
