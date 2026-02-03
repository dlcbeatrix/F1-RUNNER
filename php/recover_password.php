<?php
session_start();
require_once("../api/connessione.php");

$step = 1;
$error = "";
$success = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //cerco email
    if (isset($_POST["check_email"])) {
        $email = trim($_POST["email"]);

        $sql = "SELECT id, security_question FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->num_rows > 1) {
            die("Trovati più utenti registrati con la stessa mail");
        }
        $user = $res->fetch_assoc();

        if ($user) {
            $step = 2;
            $_SESSION['reset_user_id'] = $user["id"];
            $_SESSION['reset_question_id'] = $user["security_question"];
        } else {
            $error = "Email non trovata";
        }
    }

    //step 2, domanda di sicurezza
    if (isset($_POST["check_answer"])) {
        if (!isset($_SESSION['reset_user_id'])) {
            die("Errore sessione scaduta. Ricomincia.");
        }
        $user_id = $_SESSION['reset_user_id'];
        $answer = trim(strtolower($_POST["security_answer"]));

        $sql = "SELECT security_answer FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $data = $res->fetch_assoc();

        if ($data && password_verify($answer, $data['security_answer'])) {
            $step = 3;
            $_SESSION['can_reset_password'] = true;
        } else {
            $error = "Risposta errata";
            $step = 2;
        }
    }

    if (isset($_POST["reset_password"])) {
        if (!isset($_SESSION['can_reset_password']) || $_SESSION['can_reset_password'] !== true) {
            die("Operazione non autorizzata");
        }

        $user_id = $_SESSION["reset_user_id"];
        $new_password = $_POST["new_password"];
        $sql = "SELECT password FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $current_user_data = $res->fetch_assoc();

        if ($current_user_data && password_verify($new_password, $current_user_data["password"])) {
            $error = "La nuova password deve essere diversa dall'ultima utilizzata";
            $step = 3;
        } else {
            $new_pw_hash = password_hash($new_password, PASSWORD_DEFAULT);

            $sql_up = "UPDATE users SET password = ? WHERE id = ?";
            $stmt = $conn->prepare($sql_up);
            $stmt->bind_param("si", $new_pw_hash, $user_id);

            if ($stmt->execute()) {
                $success = "Password aggiornata con successo! <a href= 'index.php'>Accedi ora</a>";

                unset($_SESSION["reset_user_id"]);
                unset($_SESSION["reset_question_id"]);
                unset($_SESSION["can_reset_password"]);
                $step = 4;
            } else {
                $error = "Errore nel database";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recupera Password</title>
    <link rel="icon" href="../img/logo.jpg">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <div class="loginContainer">
        <h1 class="mainTitle">RECUPERA PASSWORD</h1>

        <?php if ($error): ?>
            <p class="msgBox error"> <?php echo $error; ?></p>
        <?php endif; ?>

        <?php if ($success): ?>
            <p class="msgBox success"> <?php echo $success; ?></p>
        <?php endif; ?>

        <?php if ($step === 1): ?>
            <form method="POST" action="recover_password.php">
                <p>Inserisci la tua mail: </p>
                <input type="email" name="email" placeholder="Email" required class="arcadeInput">
                <button type="submit" name="check_email" class="arcadeBtn searchBtn">CERCA ACCOUNT</button>
            </form>
            <div class="backAction">
                <a href="index.php" class="backLink">TORNA AL LOGIN</a>
            </div>
        <?php endif; ?>

        <?php if ($step === 2): ?>
            <form method="POST" action="recover_password.php">
                <p style="color: green;">Account trovato!</p>
                <label>Domanda di sicurezza: </label>
                <div class="msgBox">
                    <?php
                    $q_id = $_SESSION["reset_question_id"];
                    ?>
                    <p><?php echo isset($security_questions[$q_id]) ? $security_questions[$q_id] : "Domanda sconosciuta"; ?>
                    </p>
                </div>
                <input type="text" name="security_answer" placeholder="La tua risposta" required class="arcadeInput">
                <button type="submit" name="check_answer" class="arcadeBtn checkBtn">VERIFICA</button>
            </form>
        <?php endif; ?>

        <?php if ($step === 3): ?>
            <form method="POST" action="recover_password.php">
                <label>Nuova password: </label>
                <input type="password" name="new_password" placeholder="Nuova password" required class="arcadeInput">

                <button type="submit" name="reset_password" class="arcadeBtn selectBtn">RESETTA PASSWORD</button>
            </form>
        <?php endif; ?>
    </div>

</body>

</html>