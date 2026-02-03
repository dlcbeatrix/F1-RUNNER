<?php

session_start();

if (!isset($_SESSION["logged_in"]) || $_SESSION['logged_in'] !== true) {
    header('Location: index.php?Utente non loggato');
    exit;
}

require_once('../api/connessione.php');

$id_utente = $_SESSION['id'];

$sql = "SELECT coins, email, current_car_id, current_driver_id FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_utente);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $coins = $row["coins"];
    $email = $row["email"];
    $id_selected_car = $row["current_car_id"];
    $id_selected_driver = $row["current_driver_id"];
} else {
    $coins = 0;
    $email = "Errore";
    $id_selected_car = null;
    $id_selected_driver = null;
}
$stmt->close();

$sql = "SELECT MAX(score) AS record FROM games WHERE id_user = ?";
$stmt_record = $conn->prepare($sql);
$stmt_record->bind_param("i", $id_utente);
$stmt_record->execute();
$results = $stmt_record->get_result();
$row_record = $results->fetch_assoc();
$best_score = ($row_record["record"] === NULL) ? 0 : $row_record["record"];
$stmt_record->close();

$car_name = null;
$car_img = null;

if ($id_selected_car !== null) {
    $sql_car = "SELECT name, img FROM cars WHERE id= ?";
    $stmt_car = $conn->prepare($sql_car);
    $stmt_car->bind_param("i", $id_selected_car);
    $stmt_car->execute();
    $res_car = $stmt_car->get_result();
    if ($row_car = $res_car->fetch_assoc()) {
        $car_name = $row_car["name"];
        $car_img = $row_car["img"];
    }
    $stmt_car->close();
}

$driver_name = null;
$driver_img = null;

if ($id_selected_driver !== null) {
    $sql_driver = "SELECT name, surname, img FROM drivers WHERE id = ?";
    $stmt_driver = $conn->prepare($sql_driver);
    $stmt_driver->bind_param("i", $id_selected_driver);
    $stmt_driver->execute();
    $res_driver = $stmt_driver->get_result();
    if ($row_driver = $res_driver->fetch_assoc()) {
        $driver_name = $row_driver["name"];
        $driver_surname = $row_driver["surname"];
        $driver_img = $row_driver["img"];
    }
    $stmt_driver->close();
}

$ready_to_race = ($car_name !== null && $driver_name !== null);

$conn->close();
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="icon" href="../img/logo.jpg" type="image/jpeg">
    <link rel="stylesheet" href="../css/style.css">
    <script src="../js/sounds.js"></script>
</head>

<body>
    <?php include("../api/audio_loader.php"); ?>
    <div class="gameContainer">
        <header class="logoSection">
            <img src="../img/logo.jpg" id="f1Logo" alt="F1 Logo">
            <h1 id="homeTitle">F1 RUNNER</h1>
            <img id="globalMuteBtn" src="../img/speaker.png" alt="Mute Control">
            <div class="userStats">
                <h2>Ciao <?php echo strip_tags($_SESSION['username']); ?>!</h2>

                <div class="userStatRow">
                    <img src="../img/coin.png" alt="coins" class="icon">
                    <span>Coins: <?php echo strip_tags($coins); ?></span>
                </div>

                <div class="userStatRow">
                    <img src="../img/trophy.png" alt="Cup" class="icon" style="width: 40px; height:40px;">
                    <span>Best Score: <?php echo strip_tags($best_score); ?></span>
                </div>

            </div>
        </header>

        <div class="mainDisplay">
            <p class="lastRace">Last Setup: </p>

            <div class="setupDisplay">

                <div class=displayItem>
                    <?php if ($car_name): ?>
                        <img src="../img/cars/<?php echo strip_tags($car_img); ?>" alt="F1 Car">
                        <p><?php echo strip_tags($car_name); ?></p>
                    <?php else: ?>
                        <div class="missingSetup">No Car</div>
                    <?php endif; ?>
                </div>


                <div class="displayItem">
                    <?php if ($driver_name): ?>
                        <img src="../img/drivers/<?php echo strip_tags($driver_img); ?>" alt="Driver">
                        <p><?php echo strip_tags($driver_name) . " " . $driver_surname; ?></p>
                    <?php else: ?>
                        <div class="missingSetup">No Driver</div>
                    <?php endif; ?>
                </div>

            </div>
        </div>

        <div class="actionsBar">
            <?php if ($ready_to_race): ?>
                <a href="game.php" class="arcadeBtn playBtn">Play</a>
            <?php else: ?>
                <a href="#" class="arcadeBtn disabled">Play</a>
            <?php endif; ?>

            <a href="garage.php" class="arcadeBtn garageBtn">Garage</a>
            <a href="shop.php" class="arcadeBtn shopBtn">Shop</a>
            <a href="../api/logout.php" class="arcadeBtn exitBtn">Exit</a>
        </div>

    </div>

</body>

</html>