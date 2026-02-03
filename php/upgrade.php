<?php
session_start();

if (!isset($_SESSION["logged_in"]) || !isset($_GET['id'])) {
    header("Location: garage.php");
    exit;
}

require_once("../api/connessione.php");
require_once("../api/functions.php");

$user_id = $_SESSION["id"];
$item_id = intval($_GET["id"]);

$sql = "SELECT coins FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$my_coins = intval($row["coins"]);
$stmt->close();


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['upgrade_stat'])) {
    $stat = $_POST['upgrade_stat'];
    $col_base = str_replace('lvl_', '', $stat);

    $colonne_ammesse = ['lvl_speed', 'lvl_reliability', 'lvl_pitcrew'];

    if (!in_array($stat, $colonne_ammesse)) {
        die('Errore: statistica non valida');
    }

    $sql = "SELECT c.$col_base as base_val, g.$stat as lvl_val
            FROM cars c JOIN garage_cars g ON c.id = g.id_car
            WHERE g.id_user = ? AND g.id_car = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $item_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) {
        die("Errore: auto non trovata");
    }

    $upgrade_lvl = intval($row["lvl_val"]);
    if ($upgrade_lvl >= MAX_UPGRADE_LVL) {
        header("Location: upgrade.php?$item_id&msg= maxLevel");
        exit;
    }

    $current_lvl = intval($row["base_val"]) + intval($row["lvl_val"]);
    $next_lvl = $current_lvl + 1;
    $cost = $next_lvl * 100;

    if ($my_coins >= $cost) {
        $conn->begin_transaction();
        try {
            $sql_update = "UPDATE users SET coins = coins - ? WHERE id = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("ii", $cost, $user_id);
            $stmt_update->execute();
            $stmt_update->close();

            $sql_up_car = "UPDATE garage_cars SET $stat = $stat + 1 WHERE id_user = ? AND id_car = ?";
            $stmt_up_car = $conn->prepare($sql_up_car);
            $stmt_up_car->bind_param("ii", $user_id, $item_id);
            $stmt_up_car->execute();
            $stmt_up_car->close();

            $conn->commit();

            header("Location: upgrade.php?id=$item_id&msg=success");
            exit;
        } catch (Exception $e) {
            $conn->rollback();
            die("Errore upgrade: " . $e->getMessage());
        }
    } else {
        header("Location: upgrade.php?id=$item_id&msg=nomoney");
        exit;
    }
}

$alertClass = "";
$alertText = "";
if (isset($_GET["msg"])) {
    if ($_GET["msg"] === 'success') {
        $alertText = "UPGRADE RIUSCITO";
        $alertClass = "success";
    }
    if ($_GET["msg"] === "nomoney") {
        $alertText = "NON HAI ABBASTANZA SOLDI";
        $alertClass = "error";
    }
}

$item = null;

$sql_car = "SELECT c.name, c.img, c.speed, c.reliability, c.pitcrew,
                g.lvl_speed, g.lvl_reliability, g.lvl_pitcrew
                FROM cars c JOIN garage_cars g
                ON c.id = g.id_car
                WHERE g.id_user = ? AND c.id = ?";

$stmt_car = $conn->prepare($sql_car);
$stmt_car->bind_param("ii", $user_id, $item_id);
$stmt_car->execute();
$res = $stmt_car->get_result();
$item = $res->fetch_assoc();
$stmt_car->close();

$stats_map = [
    'speed' => [
        'label' => 'SPEED',
        'base' => intval($item['speed']),
        'level' => intval($item['lvl_speed']),
        'img' => '../img/speed.png'
    ],
    'reliability' => [
        'label' => 'RELIABILITY',
        'base' => intval($item['reliability']),
        'level' => intval($item['lvl_reliability']),
        'img' => '../img/reliability.png'
    ],
    'pitcrew' => [
        'label' => 'PITCREW',
        'base' => intval($item['pitcrew']),
        'level' => intval($item['lvl_pitcrew']),
        'img' => '../img/pitcrew.png'
    ]
];

?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upgrade</title>
    <link rel="icon" href="../img/logo.jpg" type="image/jpeg">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/carousel.css">
    <script src="../js/sounds.js"></script>
    <script src="../js/msg.js"></script>
</head>

<body>
    <?php include("../api/audio_loader.php"); ?>
    <header>
        <h1 class="mainTitle">UPGRADE</h1>
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
            <h2>CAR</h2>
            <div class="card carCard active">
                <img src="../img/cars/<?php echo htmlspecialchars($item['img']); ?>" class="pixelImage" alt="Car">
                <h3><?php echo strip_tags($item['name']); ?></h3>

                <div class="statBox">
                    <?php foreach ($stats_map as $key => $data):
                        $base = $data['base'];
                        $lvl = $data['level'];
                        $tot = $base + $lvl;
                        $max_potential = $base + 5;
                        ?>
                        <div class="statRow">
                            <span><?php echo $data['label']; ?></span>
                            <?php renderStatBar($data['base'], $data['level']); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="splitColumn">
            <div class="upgradePanel">
                <?php
                foreach ($stats_map as $key => $data):
                    $current_lvl = $data['base'] + $data['level'];
                    $next_lvl = $current_lvl + 1;
                    $cost = ($next_lvl) * 100;
                    ?>

                    <div class="upgradeRow">
                        <img src="<?php echo $data['img']; ?>" class="icon" alt="Skill Icon">
                        <div class="upgradeText">
                            <span><?php echo $data['label']; ?></span>

                            <?php if ($data['level'] < MAX_UPGRADE_LVL): ?>
                                <span class="lvlPrice">LVL <?php echo $next_lvl; ?> -PRICE: <?php echo $cost; ?> </span>
                            <?php else: ?>
                                <span class="lvlPriceMax">MAX LEVEL (<?php echo $current_lvl; ?>)</span>
                            <?php endif; ?>
                        </div>

                        <?php if ($data['level'] < MAX_UPGRADE_LVL): ?>
                            <form method="POST">
                                <input type="hidden" name="upgrade_stat" value="lvl_<?php echo $key; ?>">
                                <button class="arcadeBtn upgradeBtn">UPGRADE</button>
                            </form>
                        <?php else: ?>
                            <button class="arcadeBtn disabled">MAX LEVEL</button>
                        <?php endif; ?>
                    </div>

                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="footerAction">
        <a href="garage.php" class="arcadeBtn backBtn">BACK</a>
    </div>

</body>

</html>