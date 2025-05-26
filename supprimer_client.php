<?php
include 'bdd.php';

$client_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$confirm = isset($_GET['confirm']) && $_GET['confirm'] === 'yes';
$force = isset($_GET['force']) && $_GET['force'] === 'yes';

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $dbusername, $dbpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT * FROM client WHERE client_id = :id");
    $stmt->execute([':id' => $client_id]);
    $client = $stmt->fetch();

    if (!$client) {
        die("Client introuvable.");
    }

    if ($confirm) {
        // Vérifier s'il a des commandes ou paniers
        $panierCount = $pdo->prepare("SELECT COUNT(*) FROM panier WHERE client_id = :id");
        $panierCount->execute([':id' => $client_id]);

        $commandeCount = $pdo->prepare("SELECT COUNT(*) FROM commande WHERE client_id = :id");
        $commandeCount->execute([':id' => $client_id]);

        if (($panierCount->fetchColumn() > 0 || $commandeCount->fetchColumn() > 0) && !$force) {
            echo "Ce client a des paniers ou commandes.<br>";
            echo "<a href='supprimer_client.php?id=$client_id&confirm=yes&force=yes' class='btn btn-danger'>Forcer la suppression</a>";
            exit();
        }

        $pdo->beginTransaction();

        $pdo->prepare("DELETE FROM client_adresse WHERE client_id = :id")->execute([':id' => $client_id]);

        if ($force) {
            $pdo->prepare("DELETE FROM panier WHERE client_id = :id")->execute([':id' => $client_id]);
            $pdo->prepare("UPDATE commande SET client_id = NULL WHERE client_id = :id")->execute([':id' => $client_id]);
        }

        $pdo->prepare("DELETE FROM client WHERE client_id = :id")->execute([':id' => $client_id]);

        $pdo->commit();

        header("Location: admin_clients.php?deleted=1");
        exit();
    }

    echo "Souhaitez-vous vraiment supprimer le client : {$client['nom']} ?<br>";
    echo "<a href='?id=$client_id&confirm=yes' class='btn btn-warning'>Confirmer</a>";
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    die("Erreur : " . $e->getMessage());
}
