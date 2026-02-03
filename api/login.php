<?php
session_start();
require_once('connessione.php');

$username= $_POST['username'];
$password= $_POST['password'];

$sql= "SELECT id, username, password FROM users WHERE username = ?";

$stmt = $conn->prepare($sql); 
$stmt->bind_param("s", $username);
$stmt->execute();

$result = $stmt->get_result();

$redirect_url = "";

if($result->num_rows ===1){ //utente trovato
    $row = $result->fetch_assoc(); 

    if(password_verify($password, $row["password"])){ //password corretta
        session_regenerate_id(true);
        $_SESSION["id"] = $row["id"];
        $_SESSION["username"] = $row["username"];
        $_SESSION["logged_in"] = true;

        $redirect_url= "../php/home.php";
    } else { //password errata
        $redirect_url ="../index.php?error=Password errata";
    }
} else { //utente non trovato
    $redirect_url="../index.php?error=Utente non trovato";
}

$stmt->close();
$conn->close();

header("Location: " . $redirect_url);
exit;
?>