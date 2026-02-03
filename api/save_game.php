<?php 
session_start();

require_once("connessione.php");

//CHECK LOGIN
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Non sei loggato']);
    exit;
}

//CHECK DURATA PARTITA (ANTI CHEAT PER MONETE E PUNTI)
if (!isset($_SESSION['game_start_time'])){
    echo json_encode(['success'=> false, 'message'=> 'Partita non valida']);
    exit;
}

$end_time = time();
$start_time = $_SESSION['game_start_time'];
$duration = $end_time - $start_time;
unset($_SESSION['game_start_time']);

if ($duration < 2) {
    echo json_encode(['success' => false, 'message' => 'Partita troppo breve']);
    exit;
}

//CHECK INPUT DAL JS
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);



if (!isset($input['score']) || !isset($input['coins'])){
    echo json_encode(['success'=> false,'message'=> 'Dati mancanti'] );
    exit;
}

$user_id = $_SESSION['id'];
$claimed_score = floatval($input['score']);
$claimed_coins = intval($input['coins']);

//RECUPERO STATISTICHE DELLA MACCHINA/PILOTA

$sql = "SELECT (c.speed + g.lvl_speed) AS total_speed, (c.reliability + g.lvl_reliability) AS total_reliability,
        (c.pitcrew + g.lvl_pitcrew) AS total_pitcrew, d.talent
        FROM users u JOIN cars c ON u.current_car_id = c.id
        JOIN garage_cars g ON c.id = g.id_car AND g.id_user = u.id
        JOIN drivers d ON u.current_driver_id = d.id
        WHERE u.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id); 
$stmt->execute();
$result = $stmt->get_result();
$stats= $result->fetch_assoc();
$stmt->close();

if(!$stats){
    echo json_encode(["success"=> false,"message"=> "Errore nel recupero statistiche"] );
}

//CALCOLO MAX SCORE POSSIBILE 
$speed = intval($stats["total_speed"]);
$speed_mult = 1 + ($speed * 0.1);
$points_per_sec = 10 * $speed_mult;

$max_score = $points_per_sec * ($duration + 5);

$driver_talent = floatval($stats["talent"]);

$max_final_score = $max_score * $driver_talent;
$max_final_score += 300; //tolleranza

if ($claimed_score >= $max_final_score){
    echo json_encode(["success"=> false,"message"=> "Punteggio non valido (Anti-Cheat)"] );
    exit; 
}

//CALCOLO MAX MONETE POSSIBILI

$base_coins = floor($max_final_score / 100);

$pitcrew = intval($stats["total_pitcrew"]);
$pit_bonus = 1 + ($pitcrew * 0.1);

$max_possible_coins = floor ($base_coins * $pit_bonus);
$max_possible_coins += 30; 

if ($claimed_coins >= $max_possible_coins){
    echo json_encode(["success"=> false,"message"=> "Monete non valide (Anti-Cheat)"] );
    exit; 
}



//SALVATAGGIO 
$conn->begin_transaction();

try{
    //AGGIORNO UTENTE
    $sql_update = "UPDATE users SET coins = coins + ?,
                    best_score = GREATEST(best_score, ?) 
                    WHERE id = ?";
    $stmt = $conn->prepare($sql_update);
    $int_claimed_score = floor($claimed_score);
    $stmt->bind_param("iii", $claimed_coins, $int_claimed_score, $user_id); 
    $stmt->execute();
    $stmt->close();

    //AGGIORNO STORICO PARTITE 
    $sql_history = "INSERT INTO games (id_user, score, earned_coins, date) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql_history);
    $stmt->bind_param("iii", $user_id, $int_claimed_score, $claimed_coins); 
    $stmt->execute();
    $stmt->close();

    $conn->commit();
    echo json_encode(["success"=> true,"message"=> "Salvataggioe eseguito con successo"]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(["success"=> false,"message"=> "Errore database: ". $e->getMessage()]);
}

$conn->close();
?>