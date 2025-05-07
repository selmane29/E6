<?php
session_start();
$registrationSuccess = false; // Initialisation de la variable de succès

// Traitement du formulaire lorsqu'il est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération des données
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm-password'] ?? '';

    // Initialisation des messages d'erreur
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

    // Si pas d'erreurs, traiter l'inscription
    if (empty($errors)) {
        // Connexion à la base de données
        try {
            $conn = new PDO("mysql:host=localhost;dbname=informatique", "root", "");
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Hacher le mot de passe
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Préparer la requête d'insertion
            $stmt = $conn->prepare("INSERT INTO client (nom, email, mot_de_passe) VALUES (:nom, :email, :password)");
            $stmt->bindParam(':nom', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password_hash);

            // Exécution
            $stmt->execute();

            // Définir la variable de succès et rediriger
            $registrationSuccess = true;

            // Redirection vers la page de connexion après l'inscription réussie
            header("Location: connection.php");
            exit();  // Arrêter l'exécution après la redirection
        } catch (PDOException $e) {
            $errors[] = "Erreur : " . $e->getMessage();
        }
        $conn = null;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h1>Inscription</h1>

    <!-- Formulaire d'inscription -->
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
            <button type="submit" class="btn btn-primary">S'inscrire</button>
        </form>

        <p id="error-msg"></p>

        <!-- Affichage des erreurs -->
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
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
