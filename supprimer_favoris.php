<?php
session_start();
include 'bdd.php';

if (!isset($_SESSION['client_id']) || !isset($_POST['produit_id'])) {
    header("Location: login.php");
    exit;
}

$client_id = $_SESSION['client_id'];
$produit_id = (int) $_POST['produit_id'];

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $dbusername, $dbpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("DELETE FROM favoris WHERE client_id = :cid AND produit_id = :pid");
    $stmt->execute(['cid' => $client_id, 'pid' => $produit_id]);

    header("Location: favoris.php");
    exit;
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
