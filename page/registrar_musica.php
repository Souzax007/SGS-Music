<?php
include("../db/protecao.php");
include_once("../config/p.r.m.php");
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Gerenciar Músicas</title>
<link rel="stylesheet" href="../css/registrar_musica.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">


<script>
function toggleNovoAlbum() {
    const sel = document.getElementById("select_album");
    const box = document.getElementById("novo_album_box");

    if (sel.value === "novo_album") {
        box.classList.remove("hidden");
        box.style.display = "flex";
    } else {
        box.classList.add("hidden");
        box.style.display = "none";
    }
}
</script>
</head>
<body>
<div class="container">

    <div class="form-box-adicionar">
    <div class="teste">
         <?php include("nav.php"); ?>
    </div>

        <form method="post" enctype="multipart/form-data" class="form-music">
             <input type="hidden" name="action" value="<?= !empty($editing) ? 'edit_music' : 'add_music' ?>">
             <?php if (!empty($editing)): ?>
                 <input type="hidden" name="id_music" value="<?= $editing['id_music']; ?>">
             <?php endif; ?>

            <div class="upload-wrapper-direita">

                <label for="ds_path" class="upload-box">
                    <i class="bi bi-cloud-upload"></i>
                    <span class="musica">Selecionar música para upload</span>
                </label>

                <input type="file" id="ds_path" name="ds_path" accept="audio/*" hidden <?= empty($editing) ? 'required' : ''; ?> >

                <button type="button" id="removerArquivo" class="btn-remover d-none">
                    <i class="bi bi-x-circle"></i> Remover arquivo
                </button>

                <?php if (!empty($editing) && !empty($editing['ds_path'])): ?>
                    <div style="margin-top:8px;">
                        <small>Arquivo atual:</small>
                        <div>
                            <audio controls src="<?= '../config/' . $editing['ds_path']; ?>"></audio>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="input-grupo">
                    <input type="text" id="nm_music" name="nm_music" <?= empty($editing) ? 'disabled' : ''; ?> required placeholder="Nome da música" value="<?= htmlspecialchars($editing['nm_music'] ?? '') ?>">
                </div>

                <div class="label-album">
                    <select name="id_album" id="select_album" onchange="toggleNovoAlbum(); updateAlbumPreview();">
                        <option value="">Selecionar álbum</option>
                        <?php foreach ($albums as $al): ?>
                            <option value="<?= $al['id_album']; ?>" <?= (!empty($editing) && $editing['id_album'] == $al['id_album']) ? 'selected' : '' ?> >
                                <?= htmlspecialchars($al['nm_album']); ?>
                            </option>
                        <?php endforeach; ?>
                        <option value="novo_album">+ Criar novo álbum</option>
                    </select>
                </div>

                <div id="album_preview_box">
                    <img id="albumPreview" style="display:none; max-width:150px; max-height:150px;" alt="Capa do álbum">
                </div>

                <div id="novo_album_box" class="hidden">

                    <input type="text" name="novo_album" class="nome" placeholder="Digite o nome do novo álbum">

                    <div class="upload-wrapper-esquerda">
                        <label for="novo_cover" class="upload-box">
                            <i class="bi bi-cloud-upload"></i>
                            <img id="previewCapa">
                            <span>Enviar capa do álbum</span>
                        </label>

                        <input type="file" id="novo_cover" name="novo_cover" hidden>

                        <button type="button" id="removerCapa" class="btn-remover d-none">
                            <i class="bi bi-x-circle"></i> Remover capa
                        </button>
                    </div>

                </div>

                    <button type="submit" class="button"><?= !empty($editing) ? 'Salvar alterações' : 'Adicionar música' ?></button>

            </div>
        </form>

        <div class="input-album">

            <div class="form-box-cadastradas">
                <h3>Músicas Cadastradas</h3>

                <table>
                    <tr>
                        <th>Nome</th>
                        <th>Álbum</th>
                        <th>Ouvir</th>
                        <th>Ações</th>
                    </tr>

                    <?php foreach ($musics as $m): ?>
                    <tr>
                        <td><?= htmlspecialchars($m['nm_music']); ?></td>
                        <td><?= htmlspecialchars($m['nm_album'] ?: '—'); ?></td>
                        <td>
                            <?php if ($m['ds_path']): ?>
                                <audio controls src="<?= '../config/' . $m['ds_path']; ?>"></audio>
                            <?php endif; ?>
                        </td>
                        <td class="acoes">
                            <a href="?edit=<?= $m['id_music']; ?>" class="btn-icon editar" title="Editar">
                                <i class="bi bi-pencil-square"></i>
                            </a>

                            <a href="?action=delete&id=<?= $m['id_music']; ?>"
                            class="btn-icon excluir"
                            title="Excluir"
                            onclick="return confirm('Excluir?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>

            </div>
        </div>

    </div>

    <?php if (!empty($msg)): ?>
        <p><b><?= $msg ?></b></p>
    <?php endif; ?>
</div>



<script>
const inputFile = document.getElementById("ds_path");
const inputNome = document.getElementById("nm_music");
const uploadLabelText = document.querySelector(".upload-box .musica");
const removerBtn = document.getElementById("removerArquivo");

const inputCover = document.getElementById("novo_cover");
const removerCapaBtn = document.getElementById("removerCapa");
const coverLabelText = document.querySelector("#novo_album_box .upload-box span");
const uploadIconMusic = document.querySelector(".upload-wrapper-direita .upload-box i");
const uploadIconCover = document.querySelector("#novo_album_box .upload-box i");



inputFile.addEventListener("change", () => {
    if (inputFile.files.length > 0) {
        let fileName = inputFile.files[0].name;
        let nomeSemExtensao = fileName.replace(/\.[^/.]+$/, "");

        inputNome.disabled = false;

        if (inputNome.value.trim() === "") {
            inputNome.value = nomeSemExtensao;
        }

        uploadLabelText.textContent = fileName;

        uploadIconMusic.style.display = "none";

        removerBtn.classList.remove("d-none");
    }
});


removerBtn.addEventListener("click", () => {
    inputFile.value = "";             
    inputNome.value = "";              
    inputNome.disabled = true;         

    uploadLabelText.textContent = "Selecionar música para upload:";

    uploadIconMusic.style.display = "block";
    removerBtn.classList.add("d-none");
});

const preview = document.getElementById("previewCapa");

inputCover.addEventListener("change", () => {
    if (inputCover.files.length > 0) {
        preview.src = URL.createObjectURL(inputCover.files[0]);
        preview.style.display = "block";

        coverLabelText.style.display = "none";
        uploadIconCover.style.display = "none";

        removerCapaBtn.classList.remove("d-none");
    }
});

removerCapaBtn.addEventListener("click", () => {
    inputCover.value = "";
    preview.style.display = "none";
    preview.src = "";

    coverLabelText.style.display = "block";
    uploadIconCover.style.display = "block";

    removerCapaBtn.classList.add("d-none");
});

const selectAlbum = document.getElementById('select_album');
const albumPreview = document.getElementById('albumPreview');

var albumCovers = <?= json_encode(array_column($albums, 'ds_cover', 'id_album')); ?>;

function updateAlbumPreview(){
    const val = selectAlbum.value;
    if (!val || val === 'novo_album') {
        albumPreview.style.display = 'none';
        return;
    }
    const cover = albumCovers[val];
    if (cover) {
        albumPreview.src = '../config/' + cover;
        albumPreview.style.display = 'block';
    } else {
        albumPreview.style.display = 'none';
    }
}

updateAlbumPreview();

if (<?= !empty($editing) ? 'true' : 'false' ?>) {
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

inputFile.addEventListener("change", () => {
    if (inputFile.files.length > 0) {
        document.getElementById('nm_music').disabled = false;
    }
});


</script>


</body>
</html>
