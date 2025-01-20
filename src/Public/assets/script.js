document.addEventListener("DOMContentLoaded", function() {
    // Initialiser Swiper
    var swiper = new Swiper(".swiper-container", {
        loop: true,
        pagination: {
            el: ".swiper-pagination",
            clickable: true
        },
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev"
        }
    });

    let lastAudioPlayed = null;
    // Gestion des boutons play/pause
    const playButtons = document.querySelectorAll(".play-button");
    playButtons.forEach(button => {
        button.addEventListener("click", function() {
            var audioItem = this.closest(".audio-item");
            var audioUrl = audioItem.getAttribute("data-track");
            
            // Créer un nouvel élément audio si ce n'est pas déjà un
            var audio = audioItem.querySelector("audio");
            if (!audio) {
                audio = document.createElement("audio");
                audio.setAttribute("src", audioUrl);
                audio.setAttribute("controls", "true");
                audioItem.appendChild(audio);
                if(lastAudioPlayed && lastAudioPlayed != audio){
                    lastAudioPlayed.pause()
                    lastAudioPlayed.currentTime = 0;
                    lastAudioPlayed.setAttribute("controls", "false");
                    
                }
                lastAudioPlayed = audio;
            }

            // Si la piste est déjà en cours de lecture, on la met en pause, sinon on la joue
            if (audio.paused) {
                audio.play();
                this.innerText = "❚❚"; // Change le bouton en pause
            } else {
                audio.pause();
                this.innerText = "▶"; // Change le bouton en play
            }
        });
    });


// Fonction pour déterminer le nombre d'éléments à afficher selon la largeur de l'écran
function getNbToDisplay() {
    const screenWidth = window.innerWidth;

    if (screenWidth <= 768) {
        return 1; // Mobile
    } else if (screenWidth <= 1024) {
        return 2; // Tablette
    } else {
        return 3; // Bureau
    }
}

// Fonction pour générer et afficher les éléments dynamiquement
function displayAudios() {
    const nbToDisplay = getNbToDisplay(); // Nombre d'éléments par groupe
    const list = Math.ceil(audios.length / nbToDisplay); // Nombre total de groupes

    // Cible l'élément où insérer les groupes (par exemple, un conteneur avec l'ID "audio-container")
    const container = document.getElementById("swiper-wrapper");
    container.innerHTML = ""; // Réinitialise le contenu

    for (let i = 0; i < list; i++) {
        // Créer une div pour chaque groupe (swiper-slide)
        const slide = document.createElement("div");
        slide.className = "swiper-slide";

        const innerContainer = document.createElement("div");
        innerContainer.className = "e-con-inner";

        const elementContainer = document.createElement("div");
        elementContainer.className = "elementor-element elementor-element-47c3611 e-grid e-con-boxed e-con e-child";

        const fullContainer = document.createElement("div");
        fullContainer.className = "elementor-element elementor-element-be5272c e-grid e-con-full e-con e-child";

        // Calculer les indices pour les audios du groupe actuel
        const startIndex = i * nbToDisplay;
        const endIndex = Math.min(startIndex + nbToDisplay, audios.length);

        for (let b = startIndex; b < endIndex; b++) {
            const audioData = audios[b];

            // Créer un élément audio
            const audioElement = document.createElement("div");
            audioElement.className = "elementor-element elementor-element-acbb8b6 elementor-widget elementor-widget-video";

            const widgetContainer = document.createElement("div");
            widgetContainer.className = "elementor-widget-container";

            const hostedVideo = document.createElement("div");
            hostedVideo.className = "e-hosted-video elementor-wrapper elementor-open-inline";

            const video = document.createElement("video");
            video.className = "elementor-video mtz-vlc-mkjjc";
            video.src = audioData.file_url;
            video.controls = true;
            video.preload = "metadata";
            video.setAttribute("controlslist", "nodownload");

             // Ajouter un poster basé sur la seconde 2
             const canvas = document.createElement("canvas");
            const ctx = canvas.getContext("2d");

            // Ajouter un listener pour charger la vidéo et capturer l'image
            video.addEventListener("loadedmetadata", () => {
                video.currentTime = 2; // Aller à la seconde 2

                video.addEventListener("seeked", function captureFrame() {
                    // Définir la taille du canvas
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;

                    // Dessiner l'image de la vidéo sur le canvas
                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                    // Générer une URL pour l'image
                    const posterImage = canvas.toDataURL("image/jpeg");

                    // Définir l'image capturée comme poster
                    video.setAttribute("poster", posterImage);

                    // Retirer l'écouteur pour éviter des captures multiples
                    video.removeEventListener("seeked", captureFrame);
                });
                video.currentTime = 0;
            });

            // Ajouter l'élément vidéo à la structure
            hostedVideo.appendChild(video);
            widgetContainer.appendChild(hostedVideo);
            audioElement.appendChild(widgetContainer);
            fullContainer.appendChild(audioElement);
        }

        // Assembler et ajouter le groupe au conteneur principal
        elementContainer.appendChild(fullContainer);
        innerContainer.appendChild(elementContainer);
        slide.appendChild(innerContainer);
        container.appendChild(slide);
    }
}

// Écouteur pour redessiner les éléments si la taille d'écran change
//window.addEventListener("resize", displayAudios);

// Appel initial pour afficher les audios
displayAudios();

    
});