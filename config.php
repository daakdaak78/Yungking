<?php
// ============================================================
// config.php — À MODIFIER avec vos infos InfinityFree
// ============================================================

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ── REMPLACEZ CES 4 LIGNES AVEC VOS INFOS INFINITYFREE ──────
// Trouvez-les dans : InfinityFree Panel > MySQL Databases
$host     = 'sql300.infinityfree.com';   // ← sql300.InfinityFree 
$dbname   = 'if0_registre_church';      // ← if0_registre_church'
$username = 'if0_14686635';               // ← if0_14686635
$password = '';        // ← 
// ─────────────────────────────────────────────────────────────

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Connexion DB impossible', 'detail' => $e->getMessage()]);
    exit();
}

// Utilitaires
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function parseContact($contact) {
    if (!empty($contact) && isValidEmail($contact)) {
        return ['email' => $contact, 'phone' => null];
    }
    return ['email' => null, 'phone' => $contact ?: null];
}
?>
