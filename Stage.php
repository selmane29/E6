<?php
session_start();
include 'bdd.php';

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $dbusername, $dbpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $priceQuery = $pdo->query("SELECT MIN(prix) AS min_price, MAX(prix) AS max_price FROM produit");
    $priceResult = $priceQuery->fetch(PDO::FETCH_ASSOC);
    $minPrice = $priceResult['min_price'] ?? 0;
    $maxPrice = $priceResult['max_price'] ?? 500;

    $sql = "SELECT * FROM produit WHERE 1=1";
    $filterConditions = [];

    if (isset($_GET['filter_souris'])) {
        $filterConditions[] = "description = 'Souris'";
    }

    if (isset($_GET['filter_clavier'])) {
        $filterConditions[] = "description = 'Clavier'";
    }

    if (isset($_GET['price_min']) && isset($_GET['price_max'])) {
        $min_price = (float)$_GET['price_min'];
        $max_price = (float)$_GET['price_max'];
        $filterConditions[] = "prix BETWEEN :min_price AND :max_price";
    }

    if (!empty($filterConditions)) {
        $sql .= " AND " . implode(" AND ", $filterConditions);
    }

    $stmt = $pdo->prepare($sql);

    if (isset($min_price) && isset($max_price)) {
        $stmt->bindParam(':min_price', $min_price);
        $stmt->bindParam(':max_price', $max_price);
    }

    $stmt->execute();
    $produit = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    $produit = [];
    $minPrice = 0;
    $maxPrice = 500;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Boutique Souris et Claviers</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container mt-5">
    <h1 class="text-center mb-4">Boutique Souris et Claviers</h1>

    <!-- Filtres -->
    <form method="get" id="filterForm" class="mb-4">
        <div class="d-flex gap-4 mb-3">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="filterSouris" name="filter_souris" <?= isset($_GET['filter_souris']) ? 'checked' : '' ?>>
                <label class="form-check-label" for="filterSouris">🖱️ Souris</label>
            </div>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="filterClavier" name="filter_clavier" <?= isset($_GET['filter_clavier']) ? 'checked' : '' ?>>
                <label class="form-check-label" for="filterClavier">⌨️ Claviers</label>
            </div>
        </div>

        <div class="mb-2">
            <input type="range" name="price_min" min="<?= $minPrice ?>" max="<?= $maxPrice ?>" value="<?= $_GET['price_min'] ?? $minPrice ?>" step="1" id="minPrice">
            <input type="range" name="price_max" min="<?= $minPrice ?>" max="<?= $maxPrice ?>" value="<?= $_GET['price_max'] ?? $maxPrice ?>" step="1" id="maxPrice">
        </div>

        <div class="mb-4">
            <span>Prix min : <strong id="price-min"><?= $_GET['price_min'] ?? $minPrice ?></strong> €</span> &nbsp;
            <span>Prix max : <strong id="price-max"><?= $_GET['price_max'] ?? $maxPrice ?></strong> €</span>
        </div>
    </form>

    <!-- Produits avec espacement -->
    <div class="row g-4">
        <?php if (!empty($produit)): ?>
            <?php foreach ($produit as $p): ?>
                <div class="col-md-4 d-flex">
                    <div class="card h-100 w-100 p-2">
                        <img src="<?= htmlspecialchars($p['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($p['nom']) ?>">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div>
                                <h5 class="card-title"><?= htmlspecialchars($p['nom']) ?></h5>
                                <p class="card-text">€<?= htmlspecialchars($p['prix']) ?></p>
                            </div>
                            <a href="product_detail.php?id=<?= $p['produit_id'] ?>" class="btn btn-primary mt-2">Voir Détails</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center">Aucun produit trouvé.</p>
        <?php endif; ?>
    </div>
</div>

<?php include 'footers.php'; ?>

<script>
    const minSlider = document.getElementById('minPrice');
    const maxSlider = document.getElementById('maxPrice');
    const minPriceLabel = document.getElementById('price-min');
    const maxPriceLabel = document.getElementById('price-max');
    const filterForm = document.getElementById('filterForm');

    function updateFilters() {
        filterForm.submit();
    }

    minSlider.addEventListener('input', () => {
        if (parseInt(minSlider.value) > parseInt(maxSlider.value)) {
            maxSlider.value = minSlider.value;
        }
        minPriceLabel.textContent = minSlider.value;
        updateFilters();
    });

    maxSlider.addEventListener('input', () => {
        if (parseInt(maxSlider.value) < parseInt(minSlider.value)) {
            minSlider.value = maxSlider.value;
        }
        maxPriceLabel.textContent = maxSlider.value;
        updateFilters();
    });

    document.querySelectorAll('input[type="checkbox"]').forEach(cb => {
        cb.addEventListener('change', updateFilters);
    });
</script>

</body>
</html>
