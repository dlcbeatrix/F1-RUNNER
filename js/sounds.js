const AudioManager = {
    map: {
        click: 'sound_click',
        coin: 'sound_coin',
        crash: 'sound_crash',
        best_score: 'sound_best_score',
        game_over: 'sound_game_over',
        countdown: 'sound_countdown',
        error: 'sound_error',
        music: 'sound_music'
    },

    isMuted: false,

    init: function(){
        const savedMute = localStorage.getItem('isMuted');
        if(savedMute === 'true'){
            this.isMuted = true; 
        }
        this.applyMute();

        const musicElement = document.getElementById('sound_music');
        if(musicElement){
            musicElement.volume = 0.3; 
        }
    },

    play: function (name) {
        const id = this.map[name];
        const sound = document.getElementById(id);

        if (sound) {
            if(name !== 'music'){
                sound.currentTime = 0; 
            }

            const playPromise = sound.play();

           if(playPromise !== undefined){
            playPromise.then(() =>{
                //audio partito, non faccio niente
            }).catch(error =>{
                console.log("Audio non riprodotto, attesa tasto" + name)
            });
           }
        } else {
            console.log("Audio non trovato" + name);
        }
    },

    stop: function(name){
        const id = this.map[name];
        const sound = document.getElementById(id);
        if(sound){
            sound.pause();
            sound.currentTime = 0; 
        }
    },

    pause: function(name){
        const id = this.map[name];
        const sound = document.getElementById(id);
        if(sound && !sound.paused){
            sound.pause(); 
        }
    },

    resume: function(name){
        const id = this.map[name];
        const sound = document.getElementById(id);
        if(sound){
            const playPromise = sound.play();
            if (playPromise !== undefined) {
                playPromise.then(() => {

                }).catch(e => console.log("Audio resume bloccato: " + name));
            }
        }
    },

    applyMute: function(){
        for (let key in this.map){
            const id = this.map[key];
            const sound = document.getElementById(id);

            if(sound){
                sound.muted = this.isMuted;
            }
        }

        const muteBtn = document.getElementById('globalMuteBtn');
        if(muteBtn){
            if(this.isMuted){
                muteBtn.src = "../img/mute.png"
            } else{
                muteBtn.src = "../img/speaker.png";
            }
        }
    },

    putMute: function(){
        this.isMuted = !this.isMuted; 

        localStorage.setItem('isMuted', this.isMuted);
        this.applyMute();
    }


};

AudioManager.init();

document.addEventListener('DOMContentLoaded', () => {

    const muteBtn = document.getElementById('globalMuteBtn');
    if(muteBtn){
        AudioManager.applyMute();

        muteBtn.addEventListener('click', () =>{
            AudioManager.putMute();
        })
    }

    const buttons = document.querySelectorAll('.arcadeBtn');
    buttons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            if(!btn.classList.contains('pauseBtn')){
                AudioManager.play('click');
            }

            if (btn.tagName === 'A' && !btn.classList.contains('disabled') && btn.getAttribute('href') !== "#") {
                e.preventDefault();
                const targetUrl = btn.getAttribute('href');

                setTimeout(() => {
                    window.location.href = targetUrl;
                }, 800);
            }
        });
    });

    const urlParam = new URLSearchParams(window.location.search);
    if (urlParam.get('msg') === 'success') {
        AudioManager.play('coin');
    } else if (urlParam.get('msg') === 'nomoney'){
        AudioManager.play('error');
    }
});




