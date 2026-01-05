<?php
session_start();
include("conexao.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nm_user = trim($_POST['nm_user']);
    $ds_pass = trim($_POST['ds_pass']);

    if (empty($nm_user) || empty($ds_pass)) {
        die("Preencha todos os campos!");
    }

    $conn = db();

    $stmt = $conn->prepare("SELECT id_user, nm_user, ds_pass FROM music_user WHERE nm_user = ?");
    $stmt->bind_param("s", $nm_user);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        die("Usuário não encontrado.");
    }

    $stmt->bind_result($id_user, $usuarioDB, $senhaDB);
    $stmt->fetch();

    if (password_verify($ds_pass, $senhaDB)) {

        $_SESSION['id_user'] = $id_user;
        $_SESSION['nm_user'] = $usuarioDB;


        $_SESSION['mensagem_sucesso'] = "Conta criada com sucesso! Faça login.";
        header("Location: /music/page/main.php");
        exit;

    } else {
        die("Senha incorreta.");
    }
}
?>
