<?php
session_start();
include 'bdd.php';

if (!isset($_SESSION['client_id'])) {
    header("Location: login.php");
    exit;
}

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $dbusername, $dbpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("
        SELECT p.* FROM produit p
        JOIN favoris f ON p.produit_id = f.produit_id
        WHERE f.client_id = :client_id
    ");
    $stmt->execute(['client_id' => $_SESSION['client_id']]);
    $favoris = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    $favoris = [];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Favoris</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container mt-5">
    <h2 class="mb-4">💖 Mes produits favoris</h2>

    <?php if (count($favoris) === 0): ?>
        <div class="alert alert-info">Vous n'avez aucun produit en favoris.</div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($favoris as $produit): ?>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <img src="<?= htmlspecialchars($produit['image']) ?>" class="card-img-top" style="height: 200px; object-fit: contain;" alt="<?= htmlspecialchars($produit['nom']) ?>">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <h5 class="card-title"><?= htmlspecialchars($produit['nom']) ?></h5>
                            <p class="card-text fw-bold"><?= htmlspecialchars($produit['prix']) ?> €</p>
                            <div class="mt-auto d-grid gap-2">
                                <a href="product_detail.php?id=<?= $produit['produit_id'] ?>" class="btn btn-primary">Voir le produit</a>
                                <form method="post" action="supprimer_favoris.php">
                                    <input type="hidden" name="produit_id" value="<?= $produit['produit_id'] ?>">
                                    <button type="submit" class="btn btn-outline-danger">❌ Supprimer</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
