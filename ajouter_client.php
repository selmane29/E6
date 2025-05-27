
<?php
// Configuration de la base de données
include 'bdd.php';

$nom = '';
$email = '';
$mot_de_passe = '';
$client_adresse = '';
$ville = '';
$code_postal = '';
$pays = '';
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    $client_adresse = trim($_POST['client_adresse'] ?? '');
    $ville = trim($_POST['ville'] ?? '');
    $code_postal = trim($_POST['code_postal'] ?? '');
    $pays = trim($_POST['pays'] ?? '');

    if (empty($nom)) {
        $error = 'Le nom est requis';
    } elseif (empty($email)) {
        $error = "L'email est requis";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "L'email n'est pas valide";
    } elseif (empty($mot_de_passe)) {
        $error = 'Le mot de passe est requis';
    } else {
        try {
            $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $dbusername, $dbpassword);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM client WHERE email = :email");
            $checkStmt->bindParam(':email', $email, PDO::PARAM_STR);
            $checkStmt->execute();

            if ($checkStmt->fetchColumn() > 0) {
                $error = 'Cet email est déjà utilisé';
            } else {
                $pdo->beginTransaction();
                $hashed_password = password_hash($mot_de_passe, PASSWORD_DEFAULT);
                $insertClient = $pdo->prepare("INSERT INTO client (nom, email, mot_de_passe) VALUES (:nom, :email, :mot_de_passe)");
                $insertClient->bindParam(':nom', $nom, PDO::PARAM_STR);
                $insertClient->bindParam(':email', $email, PDO::PARAM_STR);
                $insertClient->bindParam(':mot_de_passe', $hashed_password, PDO::PARAM_STR);
                $insertClient->execute();

                $client_id = $pdo->lastInsertId();

                if (!empty($ville) && !empty($code_postal)) {
                    $insertAdresse = $pdo->prepare("INSERT INTO client_adresse (client_id, client_adresse, ville, code_postal, pays) VALUES (:client_id, :client_adresse, :ville, :code_postal, :pays)");
                    $insertAdresse->bindParam(':client_id', $client_id, PDO::PARAM_INT);
                    $insertAdresse->bindParam(':client_adresse', $client_adresse, PDO::PARAM_STR);
                    $insertAdresse->bindParam(':ville', $ville, PDO::PARAM_STR);
                    $insertAdresse->bindParam(':code_postal', $code_postal, PDO::PARAM_STR);
                    $insertAdresse->bindParam(':pays', $pays, PDO::PARAM_STR);
                    $insertAdresse->execute();
                }

                $pdo->commit();
                $message = 'Client ajouté avec succès';
                header("refresh:2;url=admin_clients.php");
                $nom = $email = $mot_de_passe = $client_adresse = $ville = $code_postal = $pays = '';
            }
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $error = $error = "Erreur lors de l'ajout du client: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un client</title>
    <style>
        * { box-sizing: border-box; font-family: Arial, sans-serif; }
        body { margin: 0; padding: 20px; background-color: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background-color: #fff; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); padding: 20px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #ddd; }
        h1 { color: #333; margin: 0; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="email"], input[type="password"] { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        .section-title { margin-top: 20px; margin-bottom: 10px; font-weight: bold; color: #555; }
        .btn { padding: 10px 15px; border: none; border-radius: 4px; font-size: 14px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-primary { background-color: #4CAF50; color: white; }
        .btn-secondary { background-color: #f0f0f0; color: #333; }
        .message { padding: 10px; margin: 20px 0; border-radius: 4px; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .buttons { margin-top: 20px; display: flex; gap: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header"><h1>Ajouter un client</h1></div>
        <?php if (!empty($message)): ?><div class="message success"><?= htmlspecialchars($message) ?></div><?php endif; ?>
        <?php if (!empty($error)): ?><div class="message error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <form method="POST" action="">
            <div class="section-title">Informations du client</div>
            <div class="form-group"><label for="nom">Nom *</label><input type="text" id="nom" name="nom" value="<?= htmlspecialchars($nom) ?>" required></div>
            <div class="form-group"><label for="email">Email *</label><input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required></div>
            <div class="form-group"><label for="mot_de_passe">Mot de passe *</label><input type="password" id="mot_de_passe" name="mot_de_passe" required></div>
            <div class="form-group"><label for="client_adresse">Adresse exacte</label><input type="text" id="client_adresse" name="client_adresse" value="<?= htmlspecialchars($client_adresse) ?>"></div>
            <div class="form-group"><label for="ville">Ville</label><input type="text" id="ville" name="ville" value="<?= htmlspecialchars($ville) ?>"></div>
            <div class="form-group"><label for="code_postal">Code postal</label><input type="text" id="code_postal" name="code_postal" value="<?= htmlspecialchars($code_postal) ?>"></div>
            <div class="form-group"><label for="pays">Pays</label><input type="text" id="pays" name="pays" value="<?= htmlspecialchars($pays) ?>"></div>
            <div class="buttons"><button type="submit" class="btn btn-primary">Ajouter</button><a href="admin_clients.php" class="btn btn-secondary">Annuler</a></div>
        </form>
    </div>
</body>
</html>
