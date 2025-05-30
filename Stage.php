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
    <title>Boutique Claviers & Souris</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- noUiSlider -->
    <link href="https://cdn.jsdelivr.net/npm/nouislider@15.7.0/dist/nouislider.min.css" rel="stylesheet">

    <style>
        body {
            font-family: "Segoe UI", sans-serif;
        }

        .amazon-sidebar {
            font-size: 0.9rem;
        }

        .amazon-sidebar h6 {
            font-size: 0.95rem;
            margin-top: 1rem;
        }

        .product-card img {
            height: 200px;
            object-fit: contain;
        }

        .card-title {
            font-size: 1.1rem;
        }

        .card-text {
            font-size: 1rem;
        }

        .amazon-sidebar ul {
            padding-left: 0;
            list-style: none;
        }

        .amazon-sidebar ul li {
            margin-bottom: 6px;
        }

        .amazon-sidebar ul li a {
            text-decoration: none;
            color: #007185;
        }

        .amazon-sidebar ul li a:hover {
            text-decoration: underline;
        }

        /* Amazon-like slider style */
        #priceSlider .noUi-connect {
            background: #017185;
        }

        #priceSlider .noUi-handle {
            border-radius: 50%;
            background-color: #017185;
            border: 2px solid white;
            box-shadow: 0 0 5px rgba(0,0,0,0.2);
            width: 20px;
            height: 20px;
            top: 0px;
        }

        #priceSlider .noUi-target {
            background: #e3e3e3;
            border: none;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container-fluid mt-4">
    <div class="row">
        <!-- Sidebar style Amazon -->
        <div class="col-md-2">
            <form method="get" id="filterForm" class="amazon-sidebar bg-white p-3 border rounded shadow-sm">
                <h6 class="fw-bold mb-2">Catégorie</h6>
                <div class="form-check mb-1">
                    <input class="form-check-input" type="checkbox" name="filter_souris" id="filterSouris" <?= isset($_GET['filter_souris']) ? 'checked' : '' ?>>
                    <label class="form-check-label small" for="filterSouris">🖱️ Souris</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="filter_clavier" id="filterClavier" <?= isset($_GET['filter_clavier']) ? 'checked' : '' ?>>
                    <label class="form-check-label small" for="filterClavier">⌨️ Claviers</label>
                </div>

                <h6 class="fw-bold mt-4 mb-2">Prix</h6>
                <div id="priceSlider" class="mb-2"></div>
                <input type="hidden" id="hiddenMinPrice" name="price_min" value="<?= $_GET['price_min'] ?? $minPrice ?>">
                <input type="hidden" id="hiddenMaxPrice" name="price_max" value="<?= $_GET['price_max'] ?? $maxPrice ?>">

                <div class="d-flex justify-content-between mb-3">
                    <small><strong id="price-min-label"><?= $_GET['price_min'] ?? $minPrice ?></strong> €</small>
                    <small><strong id="price-max-label"><?= $_GET['price_max'] ?? $maxPrice ?></strong> €</small>
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-outline-secondary btn-sm">Appliquer</button>
                </div>

                <h6 class="fw-bold mb-2">Tranches de prix</h6>
                <ul class="small">
                    <li><a href="?price_max=50">Jusqu’à 50 €</a></li>
                    <li><a href="?price_min=50&price_max=70">50 € à 70 €</a></li>
                    <li><a href="?price_min=70&price_max=100">70 € à 100 €</a></li>
                    <li><a href="?price_min=100&price_max=150">100 € à 150 €</a></li>
                    <li><a href="?price_min=150">150 € et plus</a></li>
                </ul>
            </form>
        </div>

        <!-- Zone Produits -->
        <div class="col-md-10">
            <h2 class="mb-4">Nos Produits</h2>
            <div class="row g-4">
                <?php if (!empty($produit)): ?>
                    <?php foreach ($produit as $p): ?>
                        <div class="col-sm-6 col-lg-4 d-flex">
                            <div class="card product-card h-100 w-100 p-2">
                                <img src="<?= htmlspecialchars($p['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($p['nom']) ?>">
                                <div class="card-body d-flex flex-column justify-content-between">
                                    <div>
                                        <h5 class="card-title"><?= htmlspecialchars($p['nom']) ?></h5>
                                        <p class="card-text fw-semibold">€<?= number_format($p['prix'], 2, ',', ' ') ?></p>
                                    </div>
                                    <a href="product_detail.php?id=<?= $p['produit_id'] ?>" class="btn btn-primary mt-2">Voir Détails</a>

                                    <form method="post" action="ajouter_favoris.php" class="mt-2">
                                        <input type="hidden" name="produit_id" value="<?= $p['produit_id'] ?>">
                                        <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                            ❤️ Ajouter aux favoris
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-center">Aucun produit ne correspond aux filtres sélectionnés.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'footers.php'; ?>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/nouislider@15.7.0/dist/nouislider.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const slider = document.getElementById('priceSlider');
    const hiddenMin = document.getElementById('hiddenMinPrice');
    const hiddenMax = document.getElementById('hiddenMaxPrice');
    const labelMin = document.getElementById('price-min-label');
    const labelMax = document.getElementById('price-max-label');

    noUiSlider.create(slider, {
        start: [<?= $_GET['price_min'] ?? $minPrice ?>, <?= $_GET['price_max'] ?? $maxPrice ?>],
        connect: true,
        step: 1,
        range: {
            min: <?= $minPrice ?>,
            max: <?= $maxPrice ?>
        },
        format: {
            to: function (value) {
                return Math.round(value);
            },
            from: function (value) {
                return Number(value);
            }
        }
    });

    slider.noUiSlider.on('update', (values) => {
        hiddenMin.value = values[0];
        hiddenMax.value = values[1];
        labelMin.textContent = values[0];
        labelMax.textContent = values[1];
    });
});
</script>

</body>
</html>






