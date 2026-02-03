<?php
session_start();
if (!isset($_SESSION["logged_in"]) || $_SESSION["logged_in"] !== true) {
    header('Location: ../index.php');
    exit;
}

require_once('../api/connessione.php');
require_once('../api/functions.php');

$user_id = $_SESSION['id'];

//AUTO POSSEDUTE 
$sql_cars = "SELECT c.*, gc.lvl_speed, 
    gc.lvl_reliability, gc.lvl_pitcrew
    FROM cars c  JOIN garage_cars gc 
    ON c.id = gc.id_car 
    WHERE gc.id_user = ?
    ORDER BY c.price ASC";

$stmt = $conn->prepare($sql_cars);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$res_cars = $stmt->get_result();
$my_cars = [];
while ($row = $res_cars->fetch_assoc()) {
    $my_cars[] = $row;
}
$stmt->close();

//PILOTI POSSEDUTI
$sql_drivers = 'SELECT d.*
                FROM drivers d JOIN garage_drivers gd
                ON d.id= gd.id_driver
                WHERE gd.id_user = ?
                ORDER BY d.price ASC, d.surname ASC';
$stmt = $conn->prepare($sql_drivers);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$res_drivers = $stmt->get_result();
$my_drivers = [];
while ($row = $res_drivers->fetch_assoc()) {
    $my_drivers[] = $row;
}
$stmt->close();

//SETUP ATTUALE
$sql_current = "SELECT current_car_id, current_driver_id FROM users WHERE id= ?";
$stmt = $conn->prepare($sql_current);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res_current = $stmt->get_result();
$user_data = $res_current->fetch_assoc();

$current_car = intval($user_data["current_car_id"]);
$current_driver = intval($user_data["current_driver_id"]);
$stmt->close();

//INDICI DI PARTENZA CAROSELLO
$active_car_index = isset($_GET['c']) ? intval($_GET['c']) : 0;
$active_driver_index = isset($_GET['d']) ? intval($_GET['d']) : 0;

//GESTIONE SELECT
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['select_car_id'])) {
        $new_car_id = intval($_POST['select_car_id']);
        $idx_c = isset($_POST['car_index']) ? intval($_POST['car_index']) : 0;
        $idx_d = isset($_POST['other_index']) ? intval($_POST['other_index']) : 0;

        $sql = 'UPDATE users SET current_car_id = ? WHERE id = ?';
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ii', $new_car_id, $user_id);
        $stmt->execute();
        $stmt->close();

        header("Location: garage.php?c=$idx_c&d=$idx_d");
        exit;
    }

    if (isset($_POST['select_driver_id'])) {
        $new_driver_id = intval($_POST['select_driver_id']);
        $idx_d = isset($_POST['driver_index']) ? intval($_POST['driver_index']) : 0;
        $idx_c = isset($_POST['other_index']) ? intval($_POST['other_index']) : 0;
        $sql = 'UPDATE users SET current_driver_id = ? WHERE id = ?';
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ii', $new_driver_id, $user_id);
        $stmt->execute();
        $stmt->close();

        header("Location: garage.php?c=$idx_c&d=$idx_d");
        exit;
    }
}

$stats_map = [
    'SPEED' => ['base' => 'speed', 'upgrade' => 'lvl_speed'],
    'RELIABILITY' => ['base' => 'reliability', 'upgrade' => 'lvl_reliability'],
    'PITCREW' => ['base' => 'pitcrew', 'upgrade' => 'lvl_pitcrew']
];

$conn->close();
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Garage</title>
    <link rel="icon" href="../img/logo.jpg" type="image/jpeg">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/carousel.css">
    <script src="../js/sounds.js"></script>
     <script src="../js/carousel.js"></script>
</head>

<body>
    <?php include("../api/audio_loader.php"); ?>
    <header>
        <a href="home.php" class="homeBtn"> <img src="../img/home.png" alt="Home"> </a>
        <img id="globalMuteBtn" src="../img/speaker.png" alt="Mute Control">
        <h1 class="mainTitle">GARAGE</h1>
    </header>

    <div class="splitGrid">
        <div class="splitColumn">
            <h2>CARS</h2>

            <?php if (count($my_cars) > 0): ?>
                <div class="carouselContainer">
                    <button class="arrowBtn" onclick="changeItem('car', -1)">&lt;</button>

                    <div class="carouselWindow">
                        <?php foreach ($my_cars as $index => $car): ?>
                            <div class="card carCard <?php echo $index === $active_car_index ? 'active' : ''; ?>">
                                <img src="../img/cars/<?php echo htmlspecialchars($car['img']); ?>" class="pixelImage" alt="Car Image">
                                <h3><?php echo strip_tags($car['name']); ?></h3>

                                <div class="statBox">
                                    <?php
                                    foreach ($stats_map as $label => $cols):
                                        $base_val = intval($car[$cols['base']]);
                                        $upgrade_val = intval($car[$cols['upgrade']]);
                                        ?>
                                        <div class="statRow">
                                            <span><?php echo $label; ?></span>
                                            <?php renderStatBar($base_val, $upgrade_val); ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="btnRow">
                                    <?php
                                    if ($car['id'] === $current_car):
                                        ?>
                                        <button class="arcadeBtn disabled">SELECTED</button>

                                    <?php else: ?>
                                        <form method="POST">
                                            <input type="hidden" name="select_car_id" value="<?php echo $car['id']; ?>">
                                            <input type="hidden" name="car_index" value="<?php echo $index; ?>">
                                            <input type="hidden" name="other_index" class="live-driver-index"
                                                value="<?php echo $active_driver_index; ?>">
                                            <button type="submit" class="arcadeBtn selectBtn">SELECT</button>
                                        </form>
                                    <?php endif; ?>
                                    <a href="upgrade.php?id=<?php echo $car['id']; ?>" class="arcadeBtn upgradeBtn">UPGRADE</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="arrowBtn" onclick="changeItem('car', 1)">&gt;</button>
                </div>

            <?php else: ?>
                <div class="emptyState">
                    <p>NON POSSIEDI NESSUNA AUTO. <br> ACQUISTANE UNA!</p>
                    <a href="shop.php" class="arcadeBtn shopBtn">VAI ALLO SHOP</a>
                </div>
            <?php endif; ?>
        </div>

        <div class="splitColumn">
            <h2>DRIVERS</h2>

            <?php if (count($my_drivers) > 0): ?>
                <div class="carouselContainer">
                    <button class="arrowBtn" onclick="changeItem('driver', -1)">&lt;</button>

                    <div class="carouselWindow">
                        <?php foreach ($my_drivers as $index => $driver): ?>
                            <div class="card driverCard <?php echo $index === $active_driver_index ? 'active' : ''; ?>">
                                <img src="../img/drivers/<?php echo htmlspecialchars($driver['img']); ?>" class="pixelImage" alt="Driver Image">
                                <h3><?php echo strip_tags($driver['name']). " " .strip_tags($driver['surname']); ?></h3>
                                <p class="talent">Bonus Talent: x<?php echo strip_tags($driver['talent']); ?></p>

                                <div class="btnRow">
                                    <?php
                                    if ($driver['id'] === $current_driver):
                                        ?>
                                        <button class="arcadeBtn disabled">SELECTED</button>
                                    <?php else: ?>
                                        <form method="POST">
                                            <input type="hidden" name="select_driver_id" value="<?php echo $driver['id']; ?>">
                                            <input type="hidden" name="driver_index" value="<?php echo $index; ?>">
                                            <input type="hidden" name="other_index" class="live-car-index"
                                                value="<?php echo $active_car_index; ?>">
                                            <button type="submit" class="arcadeBtn selectBtn">SELECT</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="arrowBtn" onclick="changeItem('driver', 1)">&gt;</button>
                </div>

            <?php else: ?>
                <div class="emptyState">
                    <p>NON POSSIEDI NESSUN PILOTA <br> INGAGGIA QUALCUNO!</p>
                    <a href="shop.php" class="arcadeBtn shopBtn">VAI ALLO SHOP</a>
                </div>
            <?php endif; ?>
        </div>

    </div>

    <script>
        window.startIndexes = {
            'car': <?php echo $active_car_index; ?>,
            'driver': <?php echo $active_driver_index; ?>
        };
    </script>

   

</body>

</html>