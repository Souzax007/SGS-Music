<?php
include_once("../db/conexao.php");

$conn = db();

$id_user = $_SESSION["id_user"] ?? 1; 

$sql = "SELECT 
            h.id_history,
            m.id_music,
            m.nm_music,
            h.dt_played
        FROM music_history h
        JOIN music_music m ON m.id_music = h.id_music
        WHERE h.id_user = ?
        ORDER BY h.dt_played DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '
            <a class="musica musica-hist" href="../page/tocar.php?id=' . $row['id_music'] . '" class="musica">
                <p>' . $row['nm_music'] . '</p>
            </a>';
    }
} else {
    echo "<p>Seu histórico está vazio.</p>";
}

$conn->close();
?>
