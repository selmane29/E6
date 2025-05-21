<?php
// Connexion à la base de données
$host = "localhost";
$user = "root";
$password = "";
$dbname = "informatique";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("La connexion a échoué: " . $conn->connect_error);
}
$sql = "SELECT * FROM produit ORDER BY produit_id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin - Gestion Produits</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .admin-page {
            background-color: #f5f5f5;
            padding-top: 0px;
            padding-left: 0px;
            padding-right: 0px;
        }
        .btn-add {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
        }
        .thumb {
            max-width: 80px;
            max-height: 80px;
            object-fit: contain;
        }
        .btn-edit {
            background-color: #2196F3;
            color: white;
        }
        .btn-delete {
            background-color: #F44336;
            color: white;
        }
        .description {
            max-width: 300px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>
<body class="admin-page">

<?php include 'navbaradmin.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Gestion des produits</h1>
        <a href="ajouter_produit.php" class="btn-add">+ Ajouter un produit</a>
    </div>

    <table class="table table-striped table-hover bg-white shadow-sm">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Description</th>
                <th>Prix</th>
                <th>Stock</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row["produit_id"] ?></td>
                        <td><?= htmlspecialchars($row["nom"]) ?></td>
                        <td class="description"><?= htmlspecialchars($row["description"]) ?></td>
                        <td><?= $row["prix"] ?> €</td>
                        <td><?= $row["stock"] ?></td>
                        <td><img src="<?= $row["image"] ?>" alt="<?= $row["nom"] ?>" class="thumb"></td>
                        <td>
                            <a href="modifier_produit.php?id=<?= $row["produit_id"] ?>" class="btn btn-sm btn-edit">Modifier</a>
                            <a href="supprimer_produit.php?id=<?= $row["produit_id"] ?>" class="btn btn-sm btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')">Supprimer</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7">Aucun produit trouvé</td></tr>
            <?php endif; ?>
            <?php $conn->close(); ?>
        </tbody>
    </table>
</div>

</body>
</html>
