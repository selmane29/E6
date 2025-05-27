<?php
session_start();
$registrationSuccess = false;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


require 'vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm-password'] ?? '';
    $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';

    $errors = [];

    // Vérification des champs
    if (empty($username)) {
        $errors[] = "Le nom d'utilisateur est requis.";
    }
    if (empty($email)) {
        $errors[] = "L'email est requis.";
    }
    if (empty($password)) {
        $errors[] = "Le mot de passe est requis.";
    }
    if (empty($confirmPassword) || $password !== $confirmPassword) {
        $errors[] = "Les mots de passe doivent être identiques.";
    }

    // Vérification du reCAPTCHA
    $recaptchaSecret = '6Le9YEkrAAAAAAzaNJPm_APpoux7cVzspWKiAgLC';
    $verifyResponse = file_get_contents(
        "https://www.google.com/recaptcha/api/siteverify?secret={$recaptchaSecret}&response={$recaptchaResponse}"
    );
    $responseData = json_decode($verifyResponse);

    if (!$responseData->success) {
        $errors[] = "Vérification reCAPTCHA échouée. Veuillez réessayer.";
    }

    if (empty($errors)) {
        try {
            $conn = new PDO("mysql:host=localhost;dbname=informatique", "root", "");
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
            $stmt = $conn->prepare("INSERT INTO client (nom, email, mot_de_passe) VALUES (:nom, :email, :password)");
            $stmt->bindParam(':nom', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password_hash);
            $stmt->execute();
    
            // Envoi de l'email
            $mail = new PHPMailer(true);

            $mail->SMTPDebug = 2;
            $mail->Debugoutput = 'html'; // debug du mailer
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'selmane.292002@gmail.com';
            $mail->Password   = 'dqgzvbbrwflxwzla'; // ⚠ mot de passe d'application 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
    
            $mail->setFrom('selmane.292002@gmail.com', 'Informatique.net');
            $mail->addAddress($email, $username);
    
            $mail->isHTML(true);
            $mail->Subject = 'Bienvenue chez informatique.net !';
            $mail->Body    = "<h1>Bienvenue $username !</h1><p>Merci de votre inscription.</p>";
    
            if (!$mail->send()) {
                echo "Erreur d'envoi : " . $mail->ErrorInfo;
                exit(); //exit que je viens d'ajouter
            }
    
            // ✅ Redirection si tout est OK
            header("Location: connection.php");
            exit();
    
        } catch (Exception $e) {
            echo "Erreur d'envoi : " . $e->getMessage();
        }
    }
}              
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h1>Inscription</h1>
    <?php if (!$registrationSuccess): ?>
        <form action="inscriptions.php" method="post">
            <div class="mb-3">
                <label for="username" class="form-label">Nom :</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email :</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe :</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="confirm-password" class="form-label">Confirmer le mot de passe :</label>
                <input type="password" class="form-control" id="confirm-password" name="confirm-password" required>
            </div>

            <div class="g-recaptcha mb-3" data-sitekey="6Le9YEkrAAAAAGd1U-aVxOK70P739SFxtYfxeioT"></div>

            <button type="submit" class="btn btn-primary">S'inscrire</button>
        </form>

        <p id="error-msg"></p>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger mt-3">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
    function validateForm() {
        var password = document.getElementById("password").value;
        var confirmPassword = document.getElementById("confirm-password").value;
        var errorMsg = document.getElementById("error-msg");

        if (password !== confirmPassword) {
            errorMsg.textContent = "Les mots de passe ne correspondent pas.";
            errorMsg.style.color = "red";
            return false;
        }
        return true;
    }
</script>

<?php include('footers.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
