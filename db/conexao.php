<?php
function db() {
    $host = "";
    $user = "";
    $pass = "";
    $db = "";

    $conn = new mysqli($host, $user, $pass, $db);

    if ($conn->connect_errno) {
        die("Falha na conexão: " . $conn->connect_error);
    }

    return $conn;
}
?>
