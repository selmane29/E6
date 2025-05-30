<?php
session_start();
include 'bdd.php';

if (!isset($_SESSION['client_id'])) {
    echo "OK";
    exit;
}

if (isset($_POST['produit_id'])) {
    $produit_id = (int) $_POST['produit_id'];
    $client_id = $_SESSION['client_id'];

    try {
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $dbusername, $dbpassword);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Vérifie si le favori existe déjà
        $check = $pdo->prepare("SELECT * FROM favoris WHERE client_id = :cid AND produit_id = :pid");
        $check->execute(['cid' => $client_id, 'pid' => $produit_id]);

        if ($check->rowCount() === 0) {
            $insert = $pdo->prepare("INSERT INTO favoris (client_id, produit_id) VALUES (:cid, :pid)");
            $insert->execute(['cid' => $client_id, 'pid' => $produit_id]);
        }

        // Retour à la page précédente
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
?>
