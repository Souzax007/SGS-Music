<?php
include_once("../db/conexao.php");

$conn = db();

$sql = "SELECT id_music, nm_music FROM music_music ORDER BY RAND()";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '
        <a class="musica musica-alea" href="../page/tocar.php?id=' . $row['id_music'] . '">
            <p>' . $row['nm_music'] . '</p>
        </a>';
    }
} else {
    echo "<p>Nenhuma música encontrada.</p>";
}

$conn->close();
?>
