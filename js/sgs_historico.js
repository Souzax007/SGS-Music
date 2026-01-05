document.addEventListener("click", function(e) {
    const musica = e.target.closest(".musica");
    if (!musica) return;

    const link = musica.tagName.toLowerCase() === 'a' ? musica : musica.querySelector('a');
    const href = link?.getAttribute('href') || link?.href;
    if (!href) return;

    const match = href.match(/[?&](?:id|id_music)=([^&]+)/);
    if (!match) return;
    const id = match[1];

    if (musica.classList.contains("musica-fav") ||
        musica.classList.contains("musica-alea") ||
        musica.classList.contains("musica")) {

        const url = "../config/add_history.php";
        if (navigator.sendBeacon) {
            const data = new URLSearchParams();
            data.append('id', id);
            navigator.sendBeacon(url, data);
        } else {
            fetch(url + "?id=" + encodeURIComponent(id), { keepalive: true }).catch(()=>{});
        }
    }
});
