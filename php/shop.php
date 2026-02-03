<?php
session_start();
if (!isset($_SESSION["logged_in"]) || $_SESSION["logged_in"] !== true) {
    header("Location: ../index.php");
    exit;
}

require_once("../api/connessione.php");
require_once("../api/functions.php");

$user_id = $_SESSION["id"];

//MONETE UTENTE
$sql_user = "SELECT coins FROM users WHERE id = ?";
$stmt = $conn->prepare($sql_user);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
$user_data = $res->fetch_assoc();
$my_coins = intval($user_data["coins"]);
$stmt->close();

//ARTICOLI NELLO SHOP 
$res_cars = $conn->query("SELECT * FROM cars ORDER BY price ASC, name ASC");
$all_cars = [];
while ($row = $res_cars->fetch_assoc()) {
    $all_cars[] = $row;
}

$res_drivers = $conn->query("SELECT * FROM drivers ORDER BY price ASC, surname ASC");
$all_drivers = [];
while ($row = $res_drivers->fetch_assoc()) {
    $all_drivers[] = $row;
}

//ARTICOLI GIA' IN POSSESSO 
$sql_my_cars = "SELECT id_car FROM garage_cars WHERE id_user = ?";
$stmt = $conn->prepare($sql_my_cars);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();

$all_my_cars = [];
while ($row = $res->fetch_assoc()) {
    $all_my_cars[] = $row['id_car'];
}
$stmt->close();

$sql_my_drivers = "SELECT id_driver FROM garage_drivers WHERE id_user = ?";
$stmt = $conn->prepare($sql_my_drivers);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();

$all_my_drivers = [];
while ($row = $res->fetch_assoc()) {
    $all_my_drivers[] = $row['id_driver'];
}
$stmt->close();

//INDICI CAROSELLO 
$active_car_index = isset($_GET['c']) ? intval($_GET['c']) : 0;
$active_driver_index = isset($_GET['d']) ? intval($_GET['d']) : 0;

//ACQUISTO 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['buy_car'])) {
        $car_id = intval($_POST['buy_car']);
        $idx_c = isset($_POST['car_index']) ? intval($_POST['car_index']) : 0;
        $idx_d = isset($_POST['other_index']) ? intval($_POST['other_index']) : 0;

        $sql = "SELECT price FROM cars WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $car_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $car = $res->fetch_assoc();

        if (!$car) {
            die("Errore: auto non trovata nel DB (ID: $car_id");
        }
        $price = intval($car["price"]);



        if ($my_coins >= $price) {
            $conn->begin_transaction();
            try {
                $sql_update = "UPDATE users SET coins = coins - ? WHERE id = ?";
                $stmt = $conn->prepare($sql_update);
                $stmt->bind_param("ii", $price, $user_id);
                $stmt->execute();
                $stmt->close();

                $sql_in_garage = "INSERT INTO garage_cars (id_user, id_car) VALUES (?, ?)";
                $stmt = $conn->prepare($sql_in_garage);
                $stmt->bind_param("ii", $user_id, $car_id);
                $stmt->execute();
                $stmt->close();

                $conn->commit();

                header("Location: shop.php?c=$idx_c&d=$idx_d&msg=success");
                exit;
            } catch (Exception $e) {
                $conn->rollback();
                die("Errore durante l'acquisto: " . $e->getMessage());
            }

        } else {
            header("Location: shop.php?c=$idx_c&d=$idx_d&msg=nomoney");
            exit;
        }
    }

    if (isset($_POST["buy_driver"])) {
        $driver_id = intval($_POST["buy_driver"]);
        $idx_d = isset($_POST["driver_index"]) ? intval($_POST["driver_index"]) : 0;
        $idx_c = isset($_POST["other_index"]) ? intval($_POST["other_index"]) : 0;

        $sql = "SELECT price FROM drivers WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $driver_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $driver = $res->fetch_assoc();
        if (!$driver) {
            die("Errore: pilota non trovato nel DB ID pilota: $driver_id");
        }
        $price = intval($driver["price"]);

        if ($my_coins >= $price) {
            $conn->begin_transaction();
            try {
                $sql_update = "UPDATE users SET coins = coins - ? WHERE id = ?";
                $stmt = $conn->prepare($sql_update);
                $stmt->bind_param("ii", $price, $user_id);
                $stmt->execute();
                $stmt->close();

                $sql_in_garage = "INSERT INTO garage_drivers (id_user, id_driver) VALUES (?, ?)";
                $stmt = $conn->prepare($sql_in_garage);
                $stmt->bind_param("ii", $user_id, $driver_id);
                $stmt->execute();
                $stmt->close();

                $conn->commit();

                header("Location: shop.php?c=$idx_c&d=$idx_d&msg=success");
                exit;
            } catch (Exception $e) {
                $conn->rollback();
                die("Errore durante l'acquisto: " .$e->getMessage());
            }
        } else {
            header("Location: shop.php?c=$idx_c&d=$idx_d&msg=nomoney");
            exit;
        }
    }
}


$alertClass = "";
$alertText = "";
if (isset($_GET["msg"])) {
    if ($_GET["msg"] === 'success') {
        $alertText = "ACQUISTO RIUSCITO";
        $alertClass = "success";
    }
    if ($_GET["msg"] === "nomoney") {
        $alertText = "NON HAI ABBASTANZA SOLDI";
        $alertClass = "error";
    }
}

$stats_map = [
    'SPEED' => 'speed',
    'RELIABILITY' => 'reliability',
    'PITCREW' => 'pitcrew'
];

$conn->close();
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop</title>
    <link rel="icon" href="../img/logo.jpg" type="image/jpeg">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/carousel.css">
    <script src="../js/sounds.js"></script>
    <script src="../js/carousel.js"></script>
    <script src="../js/msg.js"></script>
</head>

<body>
    <?php include("../api/audio_loader.php"); ?>
    <header>
        <h1 class="mainTitle">SHOP</h1>
        <a href="home.php" class="homeBtn"> <img src="../img/home.png" alt="Home"> </a>
        <img id="globalMuteBtn" src="../img/speaker.png" alt="Mute Control">
        <div class="coinsDisplay">
            <img src="../img/coin.png" alt="coins" class="icon">
            <span>Coins: <?php echo ($my_coins); ?></span>
        </div>
    </header>

    <div style="text-align: center; height: 60px; margin-top: 10px;">
        <?php if ($alertText): ?>
            <div class=" msgBox <?php echo $alertClass; ?>">
                <?php echo $alertText; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="splitGrid">
        <div class="splitColumn">
            <h2>CARS</h2>
            <div class="carouselContainer">
                <button class="arrowBtn" onclick="changeItem('car', -1)"> &lt;</button>

                <div class="carouselWindow">
                    <?php foreach ($all_cars as $index => $car): ?>
                        <div class="card carCard <?php echo $index === $active_car_index ? 'active' : ''; ?>">
                            <img src="../img/cars/<?php echo htmlspecialchars($car['img']); ?>" class="pixelImage" alt="Car">
                            <h3><?php echo strip_tags($car['name']); ?></h3>

                            <div class="priceTag">
                                <span>Price: <?php echo $car['price']; ?></span>
                                <img src="../img/coin.png" alt="coins" class="icon">
                            </div>

                            <div class="statBox">
                                <?php foreach ($stats_map as $label => $stat):
                                    $valore = intval($car[$stat]);
                                    ?>
                                    <div class="statRow">
                                        <span><?php echo $label; ?></span>
                                        <?php renderStatBar(($valore), 0); ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="btnRow">
                                <?php if (in_array($car['id'], $all_my_cars)): ?>
                                    <button class="arcadeBtn disabled">POSSEDUTO</button>

                                <?php elseif ($my_coins < $car['price']): ?>
                                    <button class="arcadeBtn disabled">NO MONEY</button>

                                <?php else: ?>
                                    <form method="POST">
                                        <input type="hidden" name="buy_car" value="<?php echo $car['id']; ?>">
                                        <input type="hidden" name="car_index" value="<?php echo $index; ?>">
                                        <input type="hidden" name="other_index" class="live-driver-index"
                                            value="<?php echo $active_driver_index; ?>">
                                        <button type="submit" class="arcadeBtn buyBtn">BUY</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button class="arrowBtn" onclick="changeItem('car', 1)">&gt;</button>
            </div>
        </div>

        <div class="splitColumn">
            <h2>DRIVERS</h2>
            <div class="carouselContainer">
                <button class="arrowBtn" onclick="changeItem('driver',-1)">&lt;</button>

                <div class="carouselWindow">
                    <?php foreach ($all_drivers as $index => $driver): ?>
                        <div class="card driverCard <?php echo $index === $active_driver_index ? 'active' : ""; ?>">
                            <img src="../img/drivers/<?php echo htmlspecialchars($driver['img']); ?>" class="pixelImage"
                                alt="Driver Icon">
                            <h3><?php echo strip_tags($driver['name']) . " " . strip_tags($driver['surname']); ?></h3>

                            <div class="priceTag">
                                <span>Price: <?php echo $driver['price']; ?></span>
                                <img src="../img/coin.png" alt="coins" class="icon">
                            </div>
                            <p class="talent">Talent: x<?php echo $driver['talent']; ?></p>

                            <div class="btnRow">
                                <?php if (in_array($driver['id'], $all_my_drivers)): ?>
                                    <button class="arcadeBtn disabled">POSSEDUTO</button>

                                <?php elseif ($my_coins < $driver['price']): ?>
                                    <button class="arcadeBtn disabled">NO MONEY</button>

                                <?php else: ?>
                                    <form method="POST">
                                        <input type="hidden" name="buy_driver" value="<?php echo $driver['id']; ?>">
                                        <input type="hidden" name="driver_index" value="<?php echo $index; ?>">
                                        <input type="hidden" name="other_index" class="live-car-index"
                                            value="<?php echo $active_car_index; ?>">
                                        <button type="submit" class="arcadeBtn buyBtn">BUY</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button class="arrowBtn" onclick="changeItem('driver', 1)">&gt;</button>
            </div>
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