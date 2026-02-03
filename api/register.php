<?php
    require_once('connessione.php');

    $username= $_POST['username'];
    $pattern = "/^[a-zA-Z0-9]{3,20}$/";
    if(!preg_match($pattern, $username)){
        header("Location: ../index.php?error=Username non valido");
        exit;
    };

    $email = $_POST['email'];
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        header("Location: ../index.php?error=Email non valida");
        exit;
    }


    $password= $_POST['password'];

    $security_question = intval($_POST['security_question']);
    $security_answer = trim(strtolower($_POST['security_answer']));
    $sec_answer_hash = password_hash($security_answer, PASSWORD_DEFAULT);

    $query = "SELECT id FROM users WHERE username = ? OR email = ?";
    $check = $conn->prepare($query);
    if (!$check) {
        die("Errore SQL Check: " . $conn->error);
    }
    $check->bind_param("ss", $username, $email);
    $check->execute();
    $check->store_result();

    if($check->num_rows > 0) {
        header("Location: ../index.php?error=Username o Email già usati");
        exit;
    }
    $check->close();

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $query = "INSERT INTO users (email, username, password, coins, security_question, security_answer) VALUES (?, ?, ?, 0, ?, ?)";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Errore SQL Insert: " . $conn->error);
    }

    $stmt->bind_param("sssis", $email, $username, $hashed_password, $security_question, $sec_answer_hash);

    if($stmt->execute()) {
        header("Location: ../index.php?success=Registrazione avvenuta con successo!");
    } else{
        header("Location: ../index.php?error=Errore in fase di registrazione");
    }

    $stmt->close();
    $conn->close();
    ?>