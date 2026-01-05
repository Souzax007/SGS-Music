<?php
include("../db/conexao.php");
if (session_status() === PHP_SESSION_NONE) session_start();

$conn = db();

$id_user = $_SESSION["id_user"] ?? null;

$id_music = isset($_REQUEST["id"]) ? intval($_REQUEST["id"]) : 0;

if (!$id_user) {
    http_response_code(401);
    echo "Usuário não autenticado";
    exit;
}

if ($id_music <= 0) {
    http_response_code(400);
    echo "ID inválido";
    exit;
}

$deleteDuplicates = "DELETE h1 FROM music_history h1
JOIN music_history h2
  ON h1.id_user = h2.id_user AND h1.id_music = h2.id_music
  AND (h1.dt_played < h2.dt_played OR (h1.dt_played = h2.dt_played AND h1.id_history < h2.id_history))";
$conn->query($deleteDuplicates);

$addIndex = "ALTER TABLE music_history ADD UNIQUE KEY uniq_user_music (id_user, id_music)";
@$conn->query($addIndex);

$sql = "INSERT INTO music_history (id_user, id_music, dt_played) VALUES (?, ?, NOW()) 
         ON DUPLICATE KEY UPDATE dt_played = NOW()";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id_user, $id_music);
if ($stmt->execute()) {
    http_response_code(200);
    echo "ok";
} else {
    http_response_code(500);
    echo "erro";
}

$conn->close();
?>
