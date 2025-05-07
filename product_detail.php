<?php
session_start();
include 'bdd.php';

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $dbusername, $dbpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_GET['id'])) {
        $produit_id = (int)$_GET['id'];
        $stmt = $pdo->prepare("SELECT * FROM produit WHERE produit_id = :id");
        $stmt->execute(['id' => $produit_id]);
        $produit = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$produit) {
            die("Produit introuvable.");
        }
    } else {
        die("ID de produit non spécifié.");
    }

    if (isset($_POST['add_to_cart_ajax'])) {
        if (!isset($_SESSION['username'])) {
            echo json_encode(['success' => false, 'error' => 'not_logged_in']);
            exit();
        }

        if (!isset($_SESSION['panier'])) {
            $_SESSION['panier'] = [];
        }

        $_SESSION['panier'][$produit_id] = ($_SESSION['panier'][$produit_id] ?? 0) + 1;

        echo json_encode(['success' => true]);
        exit();
    }

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($produit['nom']); ?> | Informatique.net</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            padding-top: 70px;
            font-family: "Segoe UI", sans-serif;
            background-color: #ffffff;
            color: #111;
        }

        .product-container {
            display: flex;
            flex-wrap: wrap;
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            gap: 3rem;
        }

        .product-image {
            flex: 1 1 45%;
            text-align: center;
        }

        .product-image img {
            width: 100%;
            max-width: 600px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }

        .product-details {
            flex: 1 1 45%;
        }

        .product-details h1 {
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .product-details p {
            font-size: 1rem;
            color: #444;
        }

        .product-price {
            font-size: 1.5rem;
            font-weight: bold;
            color: #028a0f;
            margin: 1.5rem 0;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }

        .btn-success {
            background-color: #028a0f;
            border: none;
        }

        .btn-success:hover {
            background-color: #026f0c;
        }

        .alert {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 1rem 2rem;
            font-weight: 500;
            border-radius: 10px;
            z-index: 1050;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            color: white;
            background-color: #028a0f;
            animation: fadeIn 0.3s ease-in-out;
        }

        .alert.error {
            background-color: #dc3545;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translate(-50%, -40%); }
            to { opacity: 1; transform: translate(-50%, -50%); }
        }

        @media (max-width: 768px) {
            .product-container {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>

<!-- ✅ NAVBAR -->
<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<nav class="navbar navbar-expand-lg fixed-top" style="background-color: #ffffff; border-bottom: 2px solid #028a0f;">
    <div class="container-fluid">
        <a class="navbar-brand" href="Stage.php" style="color: #028a0f; font-weight: bold;">Informatique.net</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon" style="color: #028a0f;"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="Stage.php">Accueil</a></li>
                <li class="nav-item"><a class="nav-link" href="profil.php">Profil</a></li>
                <li class="nav-item d-flex align-items-center">
                    <a class="nav-link" href="mon_panier.php">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#028a0f" class="bi bi-cart" viewBox="0 0 16 16">
                            <path d="M0 1.5A.5.5 0 0 1 .5 1h1a.5.5 0 0 1 .485.379L2.89 
                                     5H14.5a.5.5 0 0 1 .49.598l-1.5 7A.5.5 
                                     0 0 1 13 13H4a.5.5 0 0 1-.491-.408L1.01 
                                     2H.5a.5.5 0 0 1-.5-.5zM3.14 6l1.25 
                                     5h8.22l1.25-5H3.14zM5.5 12a1 1 0 1 0 
                                     0 2 1 1 0 0 0 0-2zm7 0a1 1 0 1 0 
                                     0 2 1 1 0 0 0 0-2z"/>
                        </svg>
                    </a>
                </li>
            </ul>
            <form class="d-flex me-3" action="search.php" method="get" style="width: 250px;">
                <input type="text" name="query" class="form-control ps-5" placeholder="Recherche un produit..." style="border: 3px solid #028a0f; border-radius: 50px;" required>
                <button type="submit" class="btn position-absolute" style="top: 50%; left: 15px; transform: translateY(-50%); background: none; border: none;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#028a0f" class="bi bi-search" viewBox="0 0 16 16">
                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001l3.85 3.85a1 1 0 0 0 
                                1.415-1.414l-3.85-3.85zm-5.242 1.356a5.5 5.5 
                                0 1 1 0-11 5.5 5.5 0 0 1 0 11z"/>
                    </svg>
                </button>
            </form>
            <?php if (isset($_SESSION['username'])): ?>
                <div class="d-flex align-items-center" style="color: #028a0f; font-weight: bold;">
                    👋 Bonjour, <?= htmlspecialchars($_SESSION['username']) ?>
                    <a href="logout.php" class="btn btn-outline-success btn-sm ms-2">Déconnexion</a>
                </div>
            <?php else: ?>
                <a href="connection.php" class="btn btn-success btn-sm">Connexion</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- ✅ CONTENU PRODUIT -->
<div class="product-container">
    <div class="product-image">
        <img src="<?= htmlspecialchars($produit['image']); ?>" alt="<?= htmlspecialchars($produit['nom']); ?>">
    </div>
    <div class="product-details">
        <h1><?= htmlspecialchars($produit['nom']); ?></h1>
        <p><?= nl2br(htmlspecialchars($produit['script'])); ?></p>
        <div class="product-price">€<?= number_format($produit['prix'], 2, ',', ' ') ?></div>
        <div class="d-flex gap-3">
            <button type="button" id="addToCartBtn" class="btn btn-success">Ajouter au Panier</button>
            <a href="catalogue.php" class="btn btn-secondary">Retour au Catalogue</a>
        </div>
    </div>
</div>

<!-- ✅ JS ALERT & REDIRECTION -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const addToCartBtn = document.getElementById('addToCartBtn');

    addToCartBtn.addEventListener('click', function() {
        const formData = new FormData();
        formData.append('add_to_cart_ajax', '1');

        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Produit ajouté au panier !');
            } else if (data.error === 'not_logged_in') {
                showAlert('Veuillez vous connecter pour ajouter au panier.', 'error');
                setTimeout(() => {
                    window.location.href = 'connection.php';
                }, 2000);
            } else {
                showAlert('Erreur lors de l\'ajout.', 'error');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showAlert('Erreur de connexion.', 'error');
        });
    });

    function showAlert(message, type = 'success') {
        const alert = document.createElement('div');
        alert.className = 'alert';
        if (type === 'error') alert.classList.add('error');
        alert.innerText = message;
        document.body.appendChild(alert);
        setTimeout(() => alert.remove(), 2500);
    }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
