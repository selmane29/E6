<?php
include 'bdd.php';

$resultsPerPage = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $resultsPerPage;

$search = isset($_GET['search']) ? $_GET['search'] : '';
$searchCondition = '';
if (!empty($search)) {
    $searchCondition = "WHERE nom LIKE :search OR email LIKE :search";
}

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $dbusername, $dbpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $countQuery = "SELECT COUNT(*) FROM client $searchCondition";
    $countStmt = $pdo->prepare($countQuery);
    if (!empty($search)) {
        $countStmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
    }
    $countStmt->execute();
    $totalClients = $countStmt->fetchColumn();
    $totalPages = ceil($totalClients / $resultsPerPage);

    $query = "SELECT c.client_id, c.nom, c.email, 
            GROUP_CONCAT(
              DISTINCT CONCAT(
                ca.client_adresse, ', ', ca.ville, ', ', ca.code_postal, ', ', ca.pays
              ) SEPARATOR ' | '
            ) AS adresses

             FROM client c
             LEFT JOIN client_adresse ca ON c.client_id = ca.client_id
             $searchCondition
             GROUP BY c.client_id
             ORDER BY c.client_id DESC
             LIMIT :offset, :limit";

    $stmt = $pdo->prepare($query);
    if (!empty($search)) {
        $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
    }
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $resultsPerPage, PDO::PARAM_INT);
    $stmt->execute();

    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des clients</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        body {
            padding-top: 0px; /* espace pour la navbar */
            background-color: #f5f5f5;
        }

        .navbar {
            min-height: 70px;
            padding-top: 10px;
            padding-bottom: 10px;
            background-color: #fff;
            border-bottom: 2px solid #028a0f;
            z-index: 1030;
        }

        .container {
            max-width: 95%;
        }

        .btn-edit {
            background-color: #0d6efd;
            color: white;
        }

        .btn-delete {
            background-color: #dc3545;
            color: white;
        }

        .pagination .active {
            background-color: #028a0f;
            color: white;
        }

        .description {
            max-width: 300px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .search-bar {
            margin-bottom: 20px;
        }

        .search-input {
            width: 300px;
            padding: 6px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .search-button {
            background-color: #028a0f;
            color: white;
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            margin-left: 5px;
        }

        .reset-search {
            margin-left: 10px;
            color: #555;
            text-decoration: underline;
        }
    </style>
</head>
<body>

<?php include 'navbaradmin.php'; ?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Liste des clients</h1>
        <a href="ajouter_client.php" class="btn btn-success">+ Ajouter un client</a>
    </div>

    <div class="search-bar mb-3">
        <form method="GET" class="d-flex align-items-center">
            <input type="text" name="search" class="search-input" placeholder="Rechercher un client..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="search-button">Rechercher</button>
            <?php if (!empty($search)): ?>
                <a href="?page=1" class="reset-search">Réinitialiser</a>
            <?php endif; ?>
        </form>
    </div>

    <table class="table table-hover bg-white shadow-sm">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Adresses</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($clients) > 0): ?>
                <?php foreach ($clients as $client): ?>
                    <tr>
                        <td><?= htmlspecialchars($client['client_id']) ?></td>
                        <td><?= htmlspecialchars($client['nom']) ?></td>
                        <td><?= htmlspecialchars($client['email']) ?></td>
                        <td><?= !empty($client['adresses']) ? htmlspecialchars($client['adresses']) : 'Aucune adresse' ?></td>
                        <td>
                            <a href="editer_client.php?id=<?= $client['client_id'] ?>" class="btn btn-sm btn-edit">Modifier</a>
                            <a href="supprimer_client.php?id=<?= $client['client_id'] ?>" class="btn btn-sm btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce client ?')">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5" class="text-center">Aucun client trouvé</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if ($totalPages > 1): ?>
        <nav class="mt-3">
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                    <li class="page-item"><a class="page-link" href="?page=1<?= !empty($search) ? '&search='.urlencode($search) : '' ?>">&laquo;</a></li>
                    <li class="page-item"><a class="page-link" href="?page=<?= $page - 1 ?><?= !empty($search) ? '&search='.urlencode($search) : '' ?>">&lsaquo;</a></li>
                <?php endif; ?>

                <?php
                $startPage = max(1, min($page - 2, $totalPages - 4));
                $endPage = min($totalPages, max($page + 2, 5));
                for ($i = $startPage; $i <= $endPage; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?><?= !empty($search) ? '&search='.urlencode($search) : '' ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <li class="page-item"><a class="page-link" href="?page=<?= $page + 1 ?><?= !empty($search) ? '&search='.urlencode($search) : '' ?>">&rsaquo;</a></li>
                    <li class="page-item"><a class="page-link" href="?page=<?= $totalPages ?><?= !empty($search) ? '&search='.urlencode($search) : '' ?>">&raquo;</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

</body>
</html>
