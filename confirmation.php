<?php
session_start();

require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Connexion à la base de données
$host = 'localhost';
$db = 'informatique';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Récupération des données du formulaire
$nom = htmlspecialchars($_POST['nom']);
$email = htmlspecialchars($_POST['email']);
$adresse = htmlspecialchars($_POST['adresse']);
$ville = htmlspecialchars($_POST['ville']);
$code_postal = htmlspecialchars($_POST['code_postal']);
$pays = htmlspecialchars($_POST['pays']);

// Vérifie si le client existe déjà
$stmt = $pdo->prepare("SELECT client_id FROM client WHERE email = ?");
$stmt->execute([$email]);
$client = $stmt->fetch();

if ($client) {
    $client_id = $client['client_id'];
} else {
    $stmt = $pdo->prepare("INSERT INTO client (nom, email, mot_de_passe) VALUES (?, ?, '')");
    $stmt->execute([$nom, $email]);
    $client_id = $pdo->lastInsertId();
}

// Insère l'adresse du client
$stmt = $pdo->prepare("INSERT INTO client_adresse (client_id, client_adresse, ville, code_postal, pays) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$client_id, $adresse, $ville, $code_postal, $pays]);
$adresse_id = $pdo->lastInsertId();

// Crée une commande
$stmt = $pdo->prepare("INSERT INTO commande (client_id, adresse_id) VALUES (?, ?)");
$stmt->execute([$client_id, $adresse_id]);
$commande_id = $pdo->lastInsertId();

// Récupère les produits du panier
$stmt = $pdo->prepare("SELECT * FROM panier WHERE client_id = ?");
$stmt->execute([$client_id]);
$panier_items = $stmt->fetchAll();

foreach ($panier_items as $item) {
    $stmt = $pdo->prepare("SELECT prix FROM produit WHERE produit_id = ?");
    $stmt->execute([$item['produit_id']]);
    $prix = $stmt->fetchColumn();

    $stmt = $pdo->prepare("INSERT INTO commande_produit (commande_id, produit_id, quantite, prix) VALUES (?, ?, ?, ?)");
    $stmt->execute([$commande_id, $item['produit_id'], $item['quantite'], $prix]);
}

// Vide le panier
$stmt = $pdo->prepare("DELETE FROM panier WHERE client_id = ?");
$stmt->execute([$client_id]);

// Envoi de l'email de confirmation
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'selmane.292002@gmail.com'; // Ton email
    $mail->Password = 'dqgzvbbrwflxwzla';      // Mot de passe d'application
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('selmane.292002@gmail.com', 'Informatique.net');
    $mail->addAddress($email, $nom);

    $mail->isHTML(true);
    $mail->Subject = 'Confirmation de votre commande';
    $mail->Body = "<h2>Merci pour votre commande, $nom !</h2>
        <p>Commande n° <strong>$commande_id</strong> enregistrée avec succès.</p>
        <p>Livraison : $adresse, $code_postal $ville, $pays</p>";

    $mail->send();
} catch (Exception $e) {
    error_log("Erreur mail : {$mail->ErrorInfo}");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 40px;
            background-color: #f3f3f3;
        }
        .confirmation {
            background-color: #fff;
            padding: 30px;
            max-width: 500px;
            margin: auto;
            box-shadow: 0 0 10px #ccc;
            border-radius: 8px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="confirmation">
    <h2>Paiement effectué avec succès !</h2>
    <p>Merci <strong><?= htmlspecialchars($nom) ?></strong> pour votre commande.</p>
    <p>Un email de confirmation sera envoyé à <strong><?= htmlspecialchars($email) ?></strong>.</p>
    </div>

</body>
</html>