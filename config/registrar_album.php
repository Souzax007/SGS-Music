<?php


require_once '../db/conexao.php'; 

$coverDir = __DIR__ . '/img/';
if (!is_dir($coverDir)) mkdir($coverDir, 0755, true);

$msg = '';

$conn = db();

function uploadCover($file, $targetDir) {
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) return null;
    if ($file['error'] !== UPLOAD_ERR_OK) return null;

    $allow = ['image/jpeg','image/png','image/gif','image/webp'];
    if (!in_array($file['type'], $allow)) return null;

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $name = uniqid('cover_') . '.' . $ext;
    $dest = rtrim($targetDir, '/') . '/' . $name;

    if (move_uploaded_file($file['tmp_name'], $dest)) {
        return 'img/' . $name; 
    }
    return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_album') {
    $nm = trim($_POST['nm_album'] ?? '');
    if ($nm === '') {
        $msg = "Nome do álbum obrigatório.";
    } else {
        $coverPath = uploadCover($_FILES['ds_cover'] ?? null, $coverDir);
        $stmt = $conn->prepare("INSERT INTO music_album (nm_album, ds_cover) VALUES (?, ?)");
        $stmt->bind_param('ss', $nm, $coverPath);
        if ($stmt->execute()) {
            $msg = "Álbum criado com sucesso.";
        } else {
            $msg = "Erro ao criar álbum: " . $conn->error;
        }
        $stmt->close();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_album') {
    $id = intval($_POST['id_album']);
    $nm = trim($_POST['nm_album'] ?? '');
    if ($nm === '') {
        $msg = "Nome do álbum obrigatório.";
    } else {
        $oldCover = null;
        $res = $conn->query("SELECT ds_cover FROM music_album WHERE id_album = $id");
        if ($res && $row = $res->fetch_assoc()) $oldCover = $row['ds_cover'];

        $newCover = uploadCover($_FILES['ds_cover'] ?? null, $coverDir);
        if ($newCover === null) $newCover = $oldCover;

        $stmt = $conn->prepare("UPDATE music_album SET nm_album = ?, ds_cover = ? WHERE id_album = ?");
        $stmt->bind_param('ssi', $nm, $newCover, $id);
        if ($stmt->execute()) {
            $msg = "Álbum atualizado.";
        } else {
            $msg = "Erro ao atualizar: " . $conn->error;
        }
        $stmt->close();
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $res = $conn->query("SELECT ds_cover FROM music_album WHERE id_album = $id");
    if ($res && $row = $res->fetch_assoc()) {
        if (!empty($row['ds_cover'])) {
            $path = __DIR__ . '/' . $row['ds_cover'];
            if (is_file($path)) @unlink($path);
        }
    }
    $stmt = $conn->prepare("DELETE FROM music_album WHERE id_album = ?");
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        $msg = "Álbum excluído.";
    } else {
        $msg = "Erro ao excluir: " . $conn->error;
    }
    $stmt->close();
}

$albums = [];
$res = $conn->query("SELECT * FROM music_album ORDER BY id_album DESC");
if ($res) {
    while ($r = $res->fetch_assoc()) $albums[] = $r;
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Registrar Albuns</title>
    <style>
        body{font-family:Arial; padding:20px;}
        table{border-collapse:collapse; width:100%;}
        table, th, td{border:1px solid #ddd;}
        th, td{padding:8px;}
        img.cover{height:60px;}
        .form-box{margin-bottom:20px; padding:12px; border:1px solid #ccc;}
    </style>
</head>
<body>
    <h2>Gerenciar Álbuns</h2>
    <?php if($msg): ?><p><strong><?php echo htmlspecialchars($msg); ?></strong></p><?php endif; ?>

    <div class="form-box">
        <h3>Adicionar Álbum</h3>
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="action" value="add_album">
            <label>Nome do álbum:<br>
                <input type="text" name="nm_album" required>
            </label><br><br>
            <label>Capa (img):<br>
                <input type="file" name="ds_cover" accept="image/*">
            </label><br><br>
            <button type="submit">Adicionar</button>
        </form>
    </div>

    <h3>Álbuns cadastrados</h3>
    <table>
        <thead><tr><th>#</th><th>Nome</th><th>Capa</th><th>Ações</th></tr></thead>
        <tbody>
            <?php foreach($albums as $a): ?>
            <tr>
                <td><?php echo $a['id_album']; ?></td>
                <td><?php echo htmlspecialchars($a['nm_album']); ?></td>
                <td><?php if($a['ds_cover']): ?><img class="cover" src="<?php echo htmlspecialchars($a['ds_cover']); ?>"><?php endif; ?></td>
                <td>
                    <a href="?edit=<?php echo $a['id_album']; ?>">Editar</a> |
                    <a href="?action=delete&id=<?php echo $a['id_album']; ?>" onclick="return confirm('Excluir álbum?')">Excluir</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php
    if (isset($_GET['edit'])):
        $editId = intval($_GET['edit']);
        $stmt = $conn->prepare("SELECT * FROM music_album WHERE id_album = ?");
        $stmt->bind_param('i', $editId);
        $stmt->execute();
        $res = $stmt->get_result();
        $album = $res->fetch_assoc();
        $stmt->close();
        if ($album):
    ?>
    <div class="form-box">
        <h3>Editar Álbum (ID <?php echo $album['id_album']; ?>)</h3>
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="action" value="edit_album">
            <input type="hidden" name="id_album" value="<?php echo $album['id_album']; ?>">
            <label>Nome do álbum:<br>
                <input type="text" name="nm_album" value="<?php echo htmlspecialchars($album['nm_album']); ?>" required>
            </label><br><br>
            <label>Capa atual:<br>
                <?php if ($album['ds_cover']): ?><img class="cover" src="<?php echo htmlspecialchars($album['ds_cover']); ?>"><?php else: ?>Nenhuma<?php endif; ?>
            </label><br><br>
            <label>Substituir capa (opcional):<br>
                <input type="file" name="ds_cover" accept="image/*">
            </label><br><br>
            <button type="submit">Salvar alterações</button>
        </form>
    </div>
    <?php
        endif;
    endif;
    $conn->close();
    ?>
</body>
</html>
