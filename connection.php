<?php
// Affichage des erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <?php include 'navbar.php'; ?>

    <h1>Connexion</h1>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $animal = $_POST['animal'] ?? '';
        $errors = [];

        if (empty($username)) $errors[] = "Le nom d'utilisateur est requis.";
        if (empty($password)) $errors[] = "Le mot de passe est requis.";
        if (empty($animal)) $errors[] = "L'animal préféré est requis.";

        if (empty($errors)) {
            $servername = "localhost";
            $dbUsername = "root";
            $dbPassword = "";
            $dbname = "informatique";

            try {
                $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbUsername, $dbPassword);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $stmt = $conn->prepare("SELECT * FROM client WHERE nom = :identifiant OR email = :identifiant");
                $stmt->bindParam(':identifiant', $username);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user && password_verify($password, $user['mot_de_passe'])) {
                    if (strtolower(trim($user['animal'])) === strtolower(trim($animal))) {
                        $_SESSION['client_id'] = $user['client_id'];
                        $_SESSION['email'] = $user['email'];
                        $_SESSION['role'] = $user['role'];

                        echo '<div class="alert alert-success">Connexion réussie !</div>';

                        if ($user['role'] === 'admin') {
                            echo '<script>window.location.href = "admin.php";</script>';
                        } else {
                            echo '<script>window.location.href = "Stage.php";</script>';
                        }
                        exit();
                    } else {
                        $errors[] = "L'animal préféré est incorrect.";
                    }
                } else {
                    $errors[] = "Nom d'utilisateur ou mot de passe incorrect.";
                }
            } catch(PDOException $e) {
                $errors[] = "Erreur : " . $e->getMessage();
            }
        }

        if (!empty($errors)) {
            echo '<div class="alert alert-danger"><ul>';
            foreach ($errors as $error) {
                echo '<li>' . htmlspecialchars($error) . '</li>';
            }
            echo '</ul></div>';
        }
    }
    ?>

    <form action="" method="post">
        <div class="mb-3">
            <label for="username" class="form-label">Nom d'utilisateur ou Email :</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Mot de passe :</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="mb-3">
            <label for="animal" class="form-label">Animal préféré :</label>
            <input type="text" class="form-control" id="animal" name="animal" required>
        </div>
        <button type="submit" class="btn btn-primary">Se connecter</button>
        <p class="mt-3">Pas encore de compte ? <a href="inscriptions.php">Inscrivez-vous ici</a>.</p>

        <?php include 'banner.php'; ?>
    </form>
</body>
</html>
