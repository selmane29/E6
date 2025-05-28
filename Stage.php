<?php
$pdo = new PDO("mysql:host=localhost;dbname=informatique;charset=utf8", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$nom = htmlspecialchars($_POST['nom'] ?? '');
$email = htmlspecialchars($_POST['email'] ?? '');
$adresse = htmlspecialchars($_POST['adresse'] ?? '');
$ville = htmlspecialchars($_POST['ville'] ?? '');
$code_postal = htmlspecialchars($_POST['code_postal'] ?? '');
$pays = htmlspecialchars($_POST['pays'] ?? '');
$carte_numero = $_POST['carte_numero'] ?? '';

if (substr($carte_numero, 0, 4) !== '4111') {
    die("Carte refusée (utilisez une carte commençant par 4111 pour test).");
}

$stmt = $pdo->prepare("SELECT client_id FROM client WHERE email = ?");
$stmt->execute([$email]);
$client = $stmt->fetch();

if (!$client) {
    die("Erreur : client inexistant.");
}

$client_id = $client['client_id'];

$stmt = $pdo->prepare("INSERT INTO client_adresse (client_id, client_adresse, ville, code_postal, pays) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$client_id, $adresse, $ville, $code_postal, $pays]);
$adresse_id = $pdo->lastInsertId();

$stmt = $pdo->prepare("INSERT INTO commande (client_id, adresse_id) VALUES (?, ?)");
$stmt->execute([$client_id, $adresse_id]);

echo "<h2>Commande simulée avec succès</h2>";
echo "<p>Merci $nom. Votre commande a été enregistrée.</p>";
?>
