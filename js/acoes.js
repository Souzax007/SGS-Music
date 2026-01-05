const musica = document.getElementById("musica");
const barraProgresso = document.getElementById("barra-progresso");
const barraContainer = document.getElementById("barra-container");

const tempoAtualElem = document.getElementById("tempo-atual");
const tempoRestanteElem = document.getElementById("tempo-restante");

const playPauseBtn = document.getElementById("playPause");

function formatarTempo(segundos) {
    const min = Math.floor(segundos / 60);
    const sec = Math.floor(segundos % 60);
    return `${min.toString().padStart(2,"0")}:${sec.toString().padStart(2,"0")}`;
}

musica.addEventListener("timeupdate", () => {
    const progresso = (musica.currentTime / musica.duration) * 100;
    barraProgresso.style.width = progresso + "%";

    tempoAtualElem.textContent = formatarTempo(musica.currentTime);
    tempoRestanteElem.textContent = "-" + formatarTempo(musica.duration - musica.currentTime);
});

barraContainer.addEventListener("click", (e) => {
    const largura = barraContainer.clientWidth;
    const clickX = e.offsetX;
    const novoTempo = (clickX / largura) * musica.duration;
    musica.currentTime = novoTempo;
});

musica.addEventListener("ended", () => {
    if(currentIndex < musics.length - 1){
        currentIndex++;
    } else {
        currentIndex = 0;
    }
    updateMusic();
});

document.getElementById("anterior").addEventListener("click", () => {
    if(currentIndex > 0){
        currentIndex--;
    } else {
        currentIndex = musics.length - 1;
    }
    updateMusic();
});

document.getElementById("proximo").addEventListener("click", () => {
    if(currentIndex < musics.length - 1){
        currentIndex++;
    } else {
        currentIndex = 0; 
    }
    updateMusic();
});