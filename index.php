<?php require_once("api/connessione.php"); ?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>F1 RUNNER</title>
    <link rel="icon" href="img/logo.jpg" type="image/jpeg">
    <link rel="stylesheet" href="css/style.css">
    <script src="js/msg.js"></script>
</head>

<body>
    <div class="notificationContainer">
        <?php
        if (isset($_GET['error'])) {
            $error = strip_tags($_GET['error']);
            echo "<div class='msgBox error'> ERRORE: " . $error . "</div>";
        }
        if (isset($_GET["success"])) {
            $success = strip_tags($_GET["success"]);
            echo "<div class='msgBox success'> " . $success . "</div>";
        }
        ?>
    </div>

    <header class="loginHeader">
        <div class="logoPictures">
            <img src="img/logo.jpg" alt="F1 Logo" style="width: 250px;">
            <img src="img/flag.png" alt="Checkered flags" style="width: 250px;">
        </div>
        <h1 class="mainTitle">F1 RUNNER</h1>
    </header>

    <div class="splitGrid">
        <div class="splitColumn">
            <h2>Accedi</h2>
            <form action="api/login.php" method="POST">
                <input type="text" name="username" placeholder="Username" class="arcadeInput" required
                    pattern="^[a-zA-Z0-9]{3,20}$">
                <input type="password" name="password" placeholder="Password" class="arcadeInput" required>
                <button type="submit" class="arcadeBtn loginBtn">Entra in pista</button>
            </form>

            <div style="margin-top: 15px; text-align: center;">
                <a href="php/recover_password.php" class="forgotLink">PASSWORD DIMENTICATA?</a>
            </div>
        </div>

        <div class="splitColumn">
            <h2>Registrati</h2>
            <form action="api/register.php" method="POST">
                <input type="email" name="email" placeholder="Email" class="arcadeInput" required>
                <input type="text" name="username" placeholder="Scegli Username" class="arcadeInput" required
                    pattern="^[a-zA-Z0-9]{3,20}$">
                <input type="password" name="password" placeholder="Scegli Password" class="arcadeInput" required>
                <div class="inputGroup">
                    <label for="security_question">Domanda di sicurezza: </label>
                    <select name="security_question" id="security_question" required class="arcadeInput">
                        <option value="" disabled selected>Scegli una domanda... </option>
                        <?php
                        foreach ($security_questions as $id => $question): ?>
                            <option value="<?php echo $id; ?>"><?php echo $question; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="inputGroup">
                    <label for="security_answer">Risposta segreta:</label>
                    <input type="text" name="security_answer" id="security_answer" placeholder="Es: Milano" required
                        class="arcadeInput">
                </div>
                <button type="submit" class="arcadeBtn registerBtn">Ottieni la tua patente!</button>
            </form>
        </div>
    </div>
    <footer>
        <a href="index.html">MANUALE UTENTE</a>
    </footer>
</body>

</html>