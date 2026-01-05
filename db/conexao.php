<?php
function db() {
    $host = "192.168.15.6";
    $user = "Marcos";
    $pass = "Marcos12007.";
    $db = "musica";

    $conn = new mysqli($host, $user, $pass, $db);

    if ($conn->connect_errno) {
        die("Falha na conexão: " . $conn->connect_error);
    }

    return $conn;
}
?>
