<?php
    include("conexao.php");

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $nm_user = trim($_POST['nm_user']);
    $ds_pass = trim($_POST['ds_pass']);

    if(empty($nm_user) or empty($ds_pass)){
        die("Preencha todos os campos");
    }

    $senhaHash = password_hash($ds_pass, PASSWORD_DEFAULT);

    $conn = db();

    $stmt = $conn->prepare("SELECT id_user FROM music_user WHERE nm_user = ?");
    $stmt->bind_param("s",$nm_user);
    $stmt->execute();
    $stmt->store_result();

    if($stmt->num_rows > 0){
        die("Este usuário não está disponivel.");
    }

    $stmt = $conn->prepare("INSERT INTO music_user (nm_user, ds_pass) VALUES (?,?)");
    $stmt->bind_param("ss",$nm_user,$senhaHash);

    if($stmt->execute()){
        echo"Usuário adicionado na faixa.";
    }else{
        echo"Algo deu errado:" . $stmt->error;
    }
    $stmt->close();
    $conn->close();
}
?>