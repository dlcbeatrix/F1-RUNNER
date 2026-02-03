<?php
session_start();
$_SESSION["game_start_time"] = time();


if (!isset($_SESSION["logged_in"]) || $_SESSION["logged_in"] !== true) {
    header("Location: ../index.php");
    exit;
}

require_once("../api/connessione.php");
$user_id = $_SESSION["id"];

$sql_score = "SELECT best_score FROM users WHERE id = ?";
$stmt_s = $conn->prepare($sql_score);
$stmt_s->bind_param("i", $user_id);
$stmt_s->execute();
$res_s = $stmt_s->get_result();
$row_s = $res_s->fetch_assoc();
$real_best_score = intval($row_s['best_score']);
$stmt_s->close();

$sql = "SELECT c.img, (c.speed + g.lvl_speed) as total_speed,
        (c.reliability + g.lvl_reliability) as total_reliability,
        (c.pitcrew + g.lvl_pitcrew) as total_pitcrew, 
        u.current_car_id as car, u.current_driver_id as driver,
        d.talent as driver_talent, d.name as driver_name, d.surname as driver_surname
        FROM cars c JOIN users u ON u.current_car_id = c.id 
        JOIN garage_cars g ON g.id_car = c.id AND g.id_user = u.id
        JOIN drivers d ON u.current_driver_id = d.id
        WHERE u.id = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Errore SQL: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();


if (!isset($data['car']) || !isset($data['driver'])) {
    header("Location: home.php?msg=Select&car&driver");
}

$js_data = [
    'img' => "../img/cars/" . $data["img"],
    'speed' => intval($data['total_speed']),
    'reliability' => intval($data['total_reliability']),
    'pitcrew' => intval($data['total_pitcrew']),
    'talent' => floatval($data['driver_talent']),
    'best_score' => $real_best_score
];

$conn->close();
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>F1 Runner</title>
    <link rel="icon" href="../img/logo.jpg" type="image/jpeg">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/game.css">
    <script src="../js/sounds.js"></script>
    <script src="../js/game.js"></script>
</head>

<body>
    <?php include("../api/audio_loader.php"); ?>
    <script>
        const GAME_DATA = <?php echo json_encode($js_data); ?>
    </script>

    <div id="scoreDisplay">
        <span>SCORE:</span><span id="scoreValue">0</span>
        <div class="lives"><span>LIVES:</span><span id="livesVal">1</span></div>
        <div class="driverInfo">
            <p>DRIVER:</p>
            <p><?php echo strip_tags($data['driver_name']) . " " . strip_tags($data['driver_surname']) ?></p>
            <p class="driverBonus">SCORE: x<?php echo $data['driver_talent'] ?></p>
        </div>
    </div>



    <div id="countdownDisplay"></div>

    <div id="gameArea" class="scrollingRoad">
        <div id="player"></div>
    </div>

    <img id="globalMuteBtn" src="../img/speaker.png" alt="Mute Control">
    <div class="btnCol">
        <button class="arcadeBtn exitBtn" onclick="askExit()">EXIT</button>
        <button class="arcadeBtn pauseBtn" onclick="pressPause()" id="pauseBtn">PAUSE</button>
    </div>



    <dialog id="gameOverDialog">
        <div class="modalContent">
            <h1 class="gameOverText">GAME OVER</h1>
            <p>SCORE : <span id="finalScore">0</span></p>

            <p id="newRecordMsg" class="new-record"> NEW BEST SCORE! </p>

            <p>COINS EARNED: + <span id="coinsEarned">0</span></p>
            <div class="btnRow">
                <button onclick="saveAndRetry()" class="arcadeBtn retryBtn" id="btnRetry">RETRY</button>
                <button onclick="saveAndExit()" class="arcadeBtn backBtn" id="btnSave">SAVE & EXIT</button>
            </div>
        </div>
    </dialog>
</body>

</html>