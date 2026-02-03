let player = null;
let gameArea = null;
let dialog = null;
let scoreDisplay = null;
let livesDisplay = null;
let countdownDisplay = null;

const lanes = [35, 50, 65];
let currentLane = 1;

let score = 0;
let lives = 1;
let gameRunning = false;
let isPaused = false;

/*TIMER e loop*/
let gameLoop; //usato per il punteggio
let spawnTimer; //genera le bombe 
let moveLoop; //loop di movimento
let speedTimer; //velocità bomba
let countdownTimer; //countdown

let speedMultiplier = 0;
let pitBonus = 0;

let obstacles = [];
let obstacleSpeed = 5;


function initGame() {
    console.log("Dati caricati: ", GAME_DATA);

    player = document.getElementById('player');
    gameArea = document.getElementById('gameArea');
    scoreDisplay = document.getElementById('scoreValue');
    livesDisplay = document.getElementById('livesVal');
    dialog = document.getElementById('gameOverDialog');
    countdownDisplay = document.getElementById('countdownDisplay');

    if(window.innerHeight < 740){
        obstacleSpeed = 3.5; 
    } else {
        obstacleSpeed = 5; 
    }

    console.log("HTML caricato");

    if (typeof GAME_DATA !== 'undefined') {
        player.style.backgroundImage = `url('${GAME_DATA.img}')`;

        lives = 1 + Math.floor(GAME_DATA.reliability / 2);
        livesDisplay.innerHTML = "&#10084;".repeat(lives);

        (GAME_DATA.speed === 1) ? speedMultiplier = 1 : speedMultiplier = 1 + (GAME_DATA.speed * 0.1);

    }
    updatePosition();

    countdownDisplay.style.display = 'flex';
    const span = document.createElement('span');
    span.id = "spanStart";
    span.textContent = "PREMI UN TASTO PER INIZIARE";
    span.style = 'font-size:30px; line-height:1.5';
    countdownDisplay.appendChild(span);

    const unlockAndStart = (e) => {
        if(e.type === 'click' && e.target.closest('button')){
            return; 
        }

        if(e.type === 'click' && e.target.closest('img')){
            return; 
        }

        window.removeEventListener('keydown', unlockAndStart);
        window.removeEventListener('click', unlockAndStart);

        if(document.getElementById('spanStart')){
            document.getElementById('spanStart').remove();
        }
        
        startCountDown();
    }

    window.addEventListener('keydown', unlockAndStart);
    window.addEventListener('click', unlockAndStart);
}

function startCountDown() {
    let count = 3;
    const p = document.createElement('p');
    p.classList.add('timer');
    p.textContent = count;
    countdownDisplay.appendChild(p);

    AudioManager.play('countdown');

    countdownTimer = setInterval(() => {
        count--;
        if (count > 0) {
            p.textContent = count;
        } else if (count === 0) {
            p.textContent = "GO!"
        } else {
            clearInterval(countdownTimer);
            countdownTimer = null; 
            countdownDisplay.style.display = 'none';
            p.remove();
            AudioManager.play('music');
            startGame();
        }
    }, 1000)
}

function startAllTimers() {
    gameLoop = setInterval(() => {
        if (gameRunning) {
            let points = 10 * speedMultiplier;
            score += points;
            scoreDisplay.textContent = Math.floor(score);
        }
    }, 1000);

    spawnTimer = setInterval(spawnObstacles, 1500);

    speedTimer = setInterval(() => {
        if (gameRunning) {
            obstacleSpeed += 0.5;
        }
    }, 2500);

    moveLoop = window.requestAnimationFrame(manageObstacles);

    gameArea.style.animationPlayState = 'running';
}

function stopAllTimers() {
    clearInterval(gameLoop);
    clearInterval(spawnTimer);
    clearInterval(speedTimer);
    window.cancelAnimationFrame(moveLoop);
    gameArea.style.animationPlayState = 'paused';
}

function startGame() {
    gameRunning = true;
    startAllTimers();
}

document.addEventListener('keydown', (e) => {
    if (!gameRunning || !player) {
        return;
    }

    if (e.key === 'ArrowLeft') {
        if (currentLane > 0) {
            currentLane--;
            updatePosition();
        }
    } else if (e.key === 'ArrowRight') {
        if (currentLane < 2) {
            currentLane++;
            updatePosition();
        }
    }
});

function updatePosition() {
    player.style.left = lanes[currentLane] + '%';
    player.style.transform = 'translateX(-50%)';
}

function spawnObstacles() {
    if (!gameRunning) {
        return;
    }

    const bomb = document.createElement('div');
    bomb.classList.add('obstacle');

    const randomLane = Math.floor(Math.random() * 3);
    bomb.style.left = lanes[randomLane] + "%";
    bomb.style.transform = 'translateX(-50%)';

    bomb.style.top = "-100px";

    bomb.dataset.lane = randomLane;
    bomb.dataset.y = -100;

    gameArea.appendChild(bomb);
    obstacles.push(bomb);
}

function manageObstacles() {
    if (!gameRunning)
        return;

    let gameHeight = gameArea.clientHeight;
    let playerHeight = player.offsetHeight;
    let playerBottomMargin = gameHeight * 0.03; //3vh

    let frontCar = gameHeight - playerHeight - playerBottomMargin; //altezza areagioco-altezza macchina
    let rearCar = gameHeight - playerBottomMargin; 

    for (let i = obstacles.length -1; i>=0; i--) {
        let bomb = obstacles[i];

        let y = parseFloat(bomb.dataset.y) + obstacleSpeed;
        bomb.dataset.y = y;
        bomb.style.top = y + 'px';

        let bombHeight = bomb.offsetHeight;
        let bombBottom = y + bombHeight;
        let bombTop = y;

        let bombTolerance = bombHeight * 0.2;
        let carTolerance = playerHeight * 0.2;

        let bombHitBoxBottom = bombBottom - bombTolerance;
        let bombHitBoxTop = bombTop + bombTolerance;

        let carHitBoxRear = rearCar - carTolerance;
        let carHitBoxFront = frontCar + carTolerance;

        //collisione: se il fondo della bomba è sceso sotto l'ala della macchina
        //se la cima della bomba è sotto il posteriore della macchina 

        if (bombHitBoxBottom > carHitBoxFront && bombHitBoxTop < carHitBoxRear) {
            let bombLane = parseInt(bomb.dataset.lane);

            if (bombLane === currentLane) {
                handleCrash(bomb, i);
                continue;
            }
        }

        if (y > gameHeight) {
            bomb.remove();
            obstacles.splice(i, 1);
        }
    }
    moveLoop = window.requestAnimationFrame(manageObstacles);
}

function handleCrash(bomb, index) {
    AudioManager.play('crash');
    bomb.remove();
    obstacles.splice(index, 1);

    lives--;
    livesDisplay.innerHTML = "&#10084;".repeat(lives);

    gameArea.style.boxShadow = "inset 0 0 50px red";
    setTimeout(() => gameArea.style.boxShadow = "none", 200);

    if (lives <= 0) {
        AudioManager.stop('music');
        gameRunning = false;
        stopAllTimers();
        setTimeout(() => {
            triggerGameOver();
        }, 1000);
    }
}

function pressPause() {
    if (!gameRunning && !isPaused) {
        return;
    }

    const btn = document.getElementById('pauseBtn');
    if (!isPaused) {
        AudioManager.pause('music');
        isPaused = true;
        gameRunning = false;

        stopAllTimers();

        btn.textContent = "RESUME";
        btn.style.backgroundColor = "green";
    } else {
        AudioManager.resume('music');
        isPaused = false;
        gameRunning = true;

        startAllTimers();

        btn.textContent = "PAUSE";
        btn.style.backgroundColor = "yellow";
    }
}

window.askExit = function () {
    AudioManager.play('click');
    AudioManager.pause('music');
    gameRunning = false;
    stopAllTimers();

    if (countdownTimer) {
        clearInterval(countdownTimer);
        countdownTimer = null;
    }
    AudioManager.stop('countdown');

    if (confirm("Vuoi uscire? Perderai i progressi attuali")) {
        AudioManager.stop('music');
        window.location.href = "home.php";
    } else {
        if (countdownDisplay.style.display === 'none') {
            if (!isPaused) {
                AudioManager.resume('music');
                gameRunning = true;
                gameArea.style.animationPlayState = 'running';

                startAllTimers();
            }
        } else if(document.querySelector('.timer')){
            document.querySelector('.timer').remove();
            startCountDown();
        }
    }
}

window.triggerGameOver = function () {

    let finalScore = score * (GAME_DATA.talent || 1);
    let intFinalScore = Math.floor(finalScore);
    let baseCoins = Math.floor(finalScore / 100);
    let pitBonus = (GAME_DATA.pitcrew === 1) ? 1 : 1 + (GAME_DATA.pitcrew * 0.1);
    let totalCoins = Math.floor(baseCoins * pitBonus);


    document.getElementById('finalScore').textContent = intFinalScore;
    document.getElementById('coinsEarned').textContent = totalCoins;

    let oldBest = GAME_DATA.best_score || 0;

    if (finalScore > oldBest) {
        AudioManager.play('best_score');
        document.getElementById('newRecordMsg').style.display = 'block';
    } else {
        AudioManager.play('game_over')
        document.getElementById('newRecordMsg').style.display = 'none';
    }

    if (typeof dialog.showModal === "function") {
        console.log("Uso Dialog Nativo");
        dialog.showModal();
    } else {
        console.log("Uso Fallback Manuale");
        dialog.classList.add('fallback-open');
    }
}
async function saveAndExit() {
    const btn = document.getElementById('btnSave'); 
    
    if(btn) {
        btn.disabled = true;
        btn.textContent = "SAVING...";
        btn.style.opacity = "0.5";
    }

    await saveData('home.php?msg=GameSaved');
}

async function saveAndRetry() {
    const btn = document.getElementById('btnRetry'); 
    
    if(btn) {
        btn.disabled = true;
        btn.textContent = "SAVING...";
        btn.style.opacity = "0.5";
    }

    await saveData(window.location.href);
}

async function saveData(redirectUrl) {
    let finalScore = parseInt(document.getElementById('finalScore').innerText);
    let earnedCoins = parseInt(document.getElementById('coinsEarned').innerText);

    let data = {
        score: finalScore,
        coins: earnedCoins
    };

    try {
        let response = await fetch('../api/save_game.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });

        let result = await response.json();

        if (result.success) {
            window.location.href = redirectUrl;
        } else {
            alert('Errore nel salvataggio' + result.message);
        }
    } catch (error) {
        console.error("Errore di rete: ", error);
        alert("Impossibile contattare il server");
    }
}

document.addEventListener('DOMContentLoaded', initGame);