<?php
require_once '../db/conexao.php';
$conn = db();

$audioDir = __DIR__ . '/audio/';
$coverDir = __DIR__ . '/img/';

if (!is_dir($audioDir)) mkdir($audioDir, 0755, true);
if (!is_dir($coverDir)) mkdir($coverDir, 0755, true);

$msg = '';
$action = $_POST['action'] ?? null;


function uploadAudio($file, $targetDir) {
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) return null;
    if ($file['error'] !== UPLOAD_ERR_OK) return null;

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $validExt = ['mp3','wav','ogg','m4a','mp4','webm'];

    if (!in_array($ext, $validExt)) return null;

    $name = uniqid('track_') . '.' . $ext;
    $dest = $targetDir . $name;

    if (move_uploaded_file($file['tmp_name'], $dest)) {
        return 'audio/' . $name;
    }
    return null;
}


function uploadCover($file, $targetDir) {
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowExt = ['jpg','jpeg','png','gif','webp','jfif'];

    if (!in_array($ext, $allowExt)) {
        return null;
    }

    $name = uniqid('cover_') . '.' . $ext;
    $dest = $targetDir . $name;

    if (move_uploaded_file($file['tmp_name'], $dest)) {
        return 'img/' . $name;
    }

    return null;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'add_music') {

    $nm_music = trim($_POST['nm_music']);
    $id_album = $_POST['id_album'];

    if ($nm_music === '') {
        $msg = "Nome da música é obrigatório.";
    } else {

        if ($id_album === 'novo_album') {

            $nm_new_album = trim($_POST['novo_album']);
            $newCover = uploadCover($_FILES['novo_cover'], $coverDir);

        if (isset($_FILES['novo_cover']) && $_FILES['novo_cover']['error'] !== UPLOAD_ERR_NO_FILE && !$newCover) {
            $msg = "Falha ao enviar capa: formato inválido (aceitos: jpg, jpeg, png, gif, webp, jfif) ou erro de upload.";
        }

        if ($nm_new_album === '') {
            $msg = "Nome do álbum é obrigatório.";
        } else {

            $stmt = $conn->prepare(
                "INSERT INTO music_album (nm_album, ds_cover) VALUES (?, ?)"
            );
            $stmt->bind_param('ss', $nm_new_album, $newCover);
            if (!$stmt->execute()) {
                $msg = "Erro ao salvar álbum: " . $stmt->error;
            } else {
                $id_album = $stmt->insert_id;
            }
        } 
    } else {
        $id_album = intval($id_album);
    }

        $ds_path = uploadAudio($_FILES['ds_path'], $audioDir);

        if (!$ds_path) {
            $msg = "Erro: selecione um arquivo de áudio válido.";
        } else {

            $stmt = $conn->prepare(
                "INSERT INTO music_music (nm_music, ds_path, id_album)
                 VALUES (?, ?, ?)"
            );
            $stmt->bind_param('ssi', $nm_music, $ds_path, $id_album);

            if ($stmt->execute()) {
                $msg = "Música adicionada com sucesso!";
            } else {
                $msg = "Erro ao salvar música.";
            }

            $stmt->close();
        }
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'edit_music') {

    $id_music = intval($_POST['id_music']);
    $nm_music = trim($_POST['nm_music']);
    $id_album = $_POST['id_album'];

    if ($id_album === 'novo_album') {

        $nm_new_album = trim($_POST['novo_album']);
        $newCover = uploadCover($_FILES['novo_cover'], $coverDir);

        if (isset($_FILES['novo_cover']) && $_FILES['novo_cover']['error'] !== UPLOAD_ERR_NO_FILE && !$newCover) {
            $msg = "Falha ao enviar capa: formato inválido (aceitos: jpg, jpeg, png, gif, webp, jfif) ou erro de upload.";
        }

        $stmt = $conn->prepare(
            "INSERT INTO music_album (nm_album, ds_cover) VALUES (?, ?)"
        );
        $stmt->bind_param('ss', $nm_new_album, $newCover);
        if (!$stmt->execute()) {
            $msg = "Erro ao salvar álbum: " . $stmt->error;
        } else {
            $id_album = $stmt->insert_id;
        }
        $stmt->close();

    } else {
        $id_album = intval($id_album);
    }

    $stmtOld = $conn->prepare("SELECT ds_path FROM music_music WHERE id_music = ?");
    $stmtOld->bind_param('i', $id_music);
    $stmtOld->execute();
    $old = $stmtOld->get_result()->fetch_assoc();
    $stmtOld->close();

    $newAudio = uploadAudio($_FILES['ds_path'], $audioDir);
    if (!$newAudio) $newAudio = $old['ds_path'];

    $stmt = $conn->prepare(
        "UPDATE music_music
         SET nm_music=?, ds_path=?, id_album=?
         WHERE id_music=?"
    );
    $stmt->bind_param('ssii', $nm_music, $newAudio, $id_album, $id_music);

    if ($stmt->execute()) {
        $msg = "Música atualizada!";
    } else {
        $msg = "Erro ao atualizar.";
    }

    $stmt->close();
}


if (isset($_GET['action']) && $_GET['action'] === 'delete') {

    $id = intval($_GET['id']);

    $stmtSel = $conn->prepare("SELECT ds_path FROM music_music WHERE id_music = ?");
    $stmtSel->bind_param('i', $id);
    $stmtSel->execute();
    $r = $stmtSel->get_result()->fetch_assoc();
    $stmtSel->close();

    if ($r && $r['ds_path']) {
        $file = __DIR__ . '/' . $r['ds_path'];
        if (file_exists($file)) unlink($file);
    }

    $stmtDel = $conn->prepare("DELETE FROM music_music WHERE id_music = ?");
    $stmtDel->bind_param('i', $id);
    $stmtDel->execute();
    $stmtDel->close();

    $msg = "Música excluída.";
}


$albums = $conn->query(
    "SELECT * FROM music_album ORDER BY nm_album ASC"
)->fetch_all(MYSQLI_ASSOC);

$musics = $conn->query("
    SELECT m.*, a.nm_album
    FROM music_music m
    LEFT JOIN music_album a ON m.id_album = a.id_album
    ORDER BY id_music DESC
")->fetch_all(MYSQLI_ASSOC);
$editing = null;
if (isset($_GET['edit'])) {
    $idEdit = intval($_GET['edit']);
    $stmtE = $conn->prepare("SELECT m.*, a.ds_cover FROM music_music m LEFT JOIN music_album a ON m.id_album = a.id_album WHERE m.id_music = ?");
    $stmtE->bind_param('i', $idEdit);
    $stmtE->execute();
    $editing = $stmtE->get_result()->fetch_assoc();
    $stmtE->close();
}?>
