<?php
$host = "127.0.0.1";
$user = "root";
$password = "";
$database = "f1_runner";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Errore di connessione: " . $conn->connect_error);
}

define("MAX_UPGRADE_LVL", 5);

$security_questions = [
    1 => "Qual è il nome del tuo primo animale domestico?",
    2 => "Qual è il cognome da nubile di tua madre?",
    3 => "In quale città sei nato?",
    4 => "Qual è il tuo film preferito?",
    5 => "Come si chiamava la tua scuola elementare?"
];