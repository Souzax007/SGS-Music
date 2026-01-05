<?php
include("../db/protecao.php");
include_once("../db/conexao.php");


$conn = db();

$id_user = $_SESSION["id_user"];

$sqlQtd = "SELECT COUNT(*) AS total FROM music_history WHERE id_user = ?";
$stmtQtd = $conn->prepare($sqlQtd);
$stmtQtd->bind_param("i", $id_user);
$stmtQtd->execute();
$resultQtd = $stmtQtd->get_result();
$rowQtd = $resultQtd->fetch_assoc();

$qtd = $rowQtd["total"];


$id_album = intval($_GET['id_album'] ?? 0);

$sql = "
    SELECT m.id_music, m.nm_music
    FROM music_music m
    WHERE m.id_album = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_album);
$stmt->execute();
$result = $stmt->get_result();


$sql = "SELECT id_album, ds_cover FROM music_album";
$result = $conn->query($sql);

$conn->close();
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>main</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css">

</head>
<body>
    <div class="container">
        <nav>
            <?php include("nav.php"); ?>
        </nav>
        <div id="bg-lottie"></div>
        <main>
            <div class="album splide">
                <div class="splide__track">
                    <ul class="splide__list">
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <li class="splide__slide">
                                <a href="tocar.php?id_album=<?= $row['id_album'] ?>">
                                    <img src="../config/<?= htmlspecialchars($row['ds_cover']) ?>" alt="Album">
                                </a>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
                </div>

                <div class="qtd_fav">
                    <p>Favorito</p>
                </div>
            <div class="favorito">
                <?php include("../config/favorito.php")?>
            </div>

            <div class="qtd_alea">
                <p>Aleatorio</p>
            </div>

            <div class="aleatorio">
                <?php include("../config/musica.php"); ?>
            </div>


             <div class="qtd_hist">
                    <p>Histórico (<?php echo $qtd; ?>)</p>
                </div>
            <div class="historico">
                <?php include("../config/historico.php"); ?>
            </div>
        </main>
    </div>

    <script src="../js/sgs_historico.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.0/lottie.min.js"></script>
    <script>
    lottie.loadAnimation({
        container: document.getElementById('bg-lottie'),
        renderer: 'svg',
        loop: true,
        autoplay: true,
        path: '../json/manimi.json'
    });
  document.addEventListener( 'DOMContentLoaded', function () {
    new Splide('.album', {
      type   : 'loop',
      perPage: 3,
      perMove: 1,
      gap: '1rem',
      autoplay: true,
      pauseOnHover: false,
    }).mount();
  });
</script>

</body>
</html>