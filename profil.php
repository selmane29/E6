<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: connection.php");
    exit();
}

$pdo = new PDO('mysql:host=localhost;dbname=informatique', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $pdo->prepare("SELECT * FROM client WHERE nom = :username OR email = :username");
$stmt->execute(['username' => $_SESSION['username']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "Utilisateur introuvable.";
    exit();
}

$success = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['update_profile'])) {
        $nom = trim($_POST['nom']);
        $email = trim($_POST['email']);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Format d'email invalide.";
        } else {
            $check = $pdo->prepare("SELECT client_id FROM client WHERE email = :email AND client_id != :client_id");
            $check->execute(['email' => $email, 'client_id' => $user['client_id']]);
            if ($check->fetch()) {
                $error = "Cet email est déjà utilisé par un autre compte.";
            } else {
                $update = $pdo->prepare("UPDATE client SET nom = :nom, email = :email WHERE client_id = :client_id");
                $update->execute([
                    'nom' => $nom,
                    'email' => $email,
                    'client_id' => $user['client_id']
                ]);
                $success = "Informations mises à jour.";
                $_SESSION['username'] = $email;
            }
        }
    }

    if (isset($_POST['update_password'])) {
        $old = $_POST['old_password'];
        $new = $_POST['new_password'];

        if (password_verify($old, $user['mot_de_passe'])) {
            if (strlen($new) < 6) {
                $error = "Le nouveau mot de passe doit contenir au moins 6 caractères.";
            } else {
                $hash = password_hash($new, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE client SET mot_de_passe = :mp WHERE client_id = :client_id");
                $stmt->execute(['mp' => $hash, 'client_id' => $user['client_id']]);
                $success = "Mot de passe mis à jour.";
            }
        } else {
            $error = "Ancien mot de passe incorrect.";
        }
    }

    // 🔄 Recharge les données actualisées
    $stmt = $pdo->prepare("SELECT * FROM client WHERE client_id = :client_id");
    $stmt->execute(['client_id' => $user['client_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Profil client</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .profile-avatar {
            width: 100px;
            height: 100px;
            background-color: #ccc;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
<div class="container mt-5 d-flex">
    <!-- Avatar + nom -->
    <div class="me-5 text-center">
        <div class="profile-avatar">
            <i class="bi bi-person"></i>
        </div>
        <h5><?= htmlspecialchars($user['nom']) ?></h5>
    </div>

    <!-- Contenu principal -->
    <div class="flex-fill">
        <h2>Votre profil</h2>
        <p class="text-muted">Modifiez vos informations personnelles à tout moment.</p>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php elseif ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="accordion" id="accordionProfil">

            <!-- Infos personnelles -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button" data-bs-toggle="collapse" data-bs-target="#collapseInfo">
                        Informations personnelles
                    </button>
                </h2>
                <div id="collapseInfo" class="accordion-collapse collapse show">
                    <div class="accordion-body">
                        <form method="post">
                            <div class="mb-3">
                                <label class="form-label">Nom :</label>
                                <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($user['nom']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email :</label>
                                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                            </div>
                            <button type="submit" name="update_profile" class="btn btn-primary">Mettre à jour</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Mot de passe -->
            <div class="accordion-item mt-3">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#collapsePassword">
                        Modifier le mot de passe
                    </button>
                </h2>
                <div id="collapsePassword" class="accordion-collapse collapse">
                    <div class="accordion-body">
                        <form method="post">
                            <div class="mb-3">
                                <label class="form-label">Mot de passe actuel :</label>
                                <input type="password" name="old_password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nouveau mot de passe :</label>
                                <input type="password" name="new_password" class="form-control" required>
                            </div>
                            <button type="submit" name="update_password" class="btn btn-warning">Changer le mot de passe</button>
                        </form>
                    </div>
                </div>
            </div>

        </div>

        <!-- 🔙 Bouton retour en bas -->
        <div class="mt-4">
            <a href="stage.php" class="btn btn-secondary">Retour à l'accueil</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
