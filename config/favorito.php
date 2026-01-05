<?php
include_once("../db/conexao.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$conn = db();

$id_user = $_SESSION["id_user"] ?? 1;

$sql = "SELECT 
            f.id_music,
            m.nm_music
        FROM music_favorites f
        JOIN music_music m ON m.id_music = f.id_music
        WHERE f.id_user = ?
        ORDER BY m.nm_music ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
          echo '
        <a class="musica musica-fav" href="../page/tocar.php?id=' . $row['id_music'] . '">
            <p>' . $row['nm_music'] . '</p>
        </a>';
    }
} else {
    echo "<p>Você ainda não tem músicas favoritas.</p>";
}

$conn->close();
?>
