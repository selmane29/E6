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

    $is_logged_in = isset($_SESSION['username']);

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
        .page-produit {
            padding-top: 70px;
            font-family: "Segoe UI", sans-serif;
            background-color: #ffffff;
            color: #111;
        }

        .page-produit .product-container {
            display: flex;
            flex-wrap: wrap;
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            gap: 3rem;
        }

        .page-produit .product-image {
            flex: 1 1 45%;
            text-align: center;
        }

        .page-produit .product-image img {
            width: 100%;
            max-width: 600px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }

        .page-produit .product-details {
            flex: 1 1 45%;
        }

        .page-produit .product-details h1 {
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .page-produit .product-details p {
            font-size: 1rem;
            color: #444;
        }

        .page-produit .product-price {
            font-size: 1.5rem;
            font-weight: bold;
            color: #028a0f;
            margin: 1.5rem 0;
        }

        .page-produit .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }

        .page-produit .btn-success {
            background-color: #028a0f;
            border: none;
        }

        .page-produit .btn-success:hover {
            background-color: #026f0c;
        }

        .page-produit .alert {
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

        .page-produit .alert.error {
            background-color: #dc3545;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translate(-50%, -40%); }
            to { opacity: 1; transform: translate(-50%, -50%); }
        }

        @media (max-width: 768px) {
            .page-produit .product-container {
                flex-direction: column;
            }
        }
    </style>
</head>
<body class="page-produit">

<?php include 'navbar.php'; ?>

<div class="product-container">
    <div class="product-image">
        <img src="<?= htmlspecialchars($produit['image']); ?>" alt="<?= htmlspecialchars($produit['nom']); ?>">
    </div>
    <div class="product-details">
        <h1><?= htmlspecialchars($produit['nom']); ?></h1>
        <p><?= nl2br(htmlspecialchars($produit['script'])); ?></p>
        <div class="product-price">€<?= number_format($produit['prix'], 2, ',', ' ') ?></div>
        <div class="d-flex gap-3">
            <button type="button" id="addToCartBtn" class="btn btn-success" <?= !$is_logged_in ? 'data-not-logged-in="1"' : '' ?>>
                Ajouter au Panier
            </button>
            <a href="Stage.php" class="btn btn-secondary">Retour au Catalogue</a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const addToCartBtn = document.getElementById('addToCartBtn');

    if (addToCartBtn.dataset.notLoggedIn) {
        addToCartBtn.addEventListener('click', function() {
            showAlert('⚠️ Vous devez être connecté pour ajouter ce produit au panier.', 'error');
            setTimeout(() => window.location.href = 'connection.php', 500);
        });
        return;
    }

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
                showAlert('⚠️ Vous devez être connecté pour ajouter ce produit au panier.', 'error');
                setTimeout(() => window.location.href = 'connection.php', 3000);
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

<?php include 'banner.php'; ?>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
