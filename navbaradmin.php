<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="navbar navbar-expand-lg" style="background-color: #ffffff; border-bottom: 2px solid #028a0f;">
    <div class="container-fluid">
        <a class="navbar-brand" href="admin.php" style="color: #028a0f; font-weight: bold;">Informatique.net</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="admin.php">Catalogue</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="mon_panier.php">Panier</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="stock.php">Gérer les stocks</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin_clients.php">Gérer les utilisateurs</a>
                </li>
            </ul>

            <form class="d-flex me-3 position-relative" action="search.php" method="get" style="width: 250px;">
                <input type="text" name="query" placeholder="Recherche un produit..." 
                       class="form-control ps-5 rounded-pill border-success" required>
                <button type="submit" class="btn position-absolute" 
                        style="top: 50%; left: 15px; transform: translateY(-50%); background: none; border: none;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#028a0f" class="bi bi-search" viewBox="0 0 16 16">
                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398l3.85 3.85a1 1 0 0 0 
                                 1.415-1.414l-3.85-3.85zm-5.242 1.356a5.5 5.5 
                                 0 1 1 0-11 5.5 5.5 0 0 1 0 11z"/>
                    </svg>
                </button>
            </form>

            <?php if (isset($_SESSION['username'])): ?>
                <div class="d-flex align-items-center fw-bold text-success">
                    👋 Bonjour, <?= htmlspecialchars($_SESSION['username']) ?> &nbsp;
                    <a href="logout.php" class="btn btn-outline-success btn-sm ms-2">Déconnexion</a>
                </div>
            <?php else: ?>
                <a href="connection.php" class="btn btn-success btn-sm">Connexion</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<style>
    .navbar .nav-link {
        color: #028a0f !important;
        font-weight: bold;
    }
    .navbar .nav-link:hover {
        color: #026f0c !important;
    }
    .navbar-brand {
        color: #028a0f !important;
        font-size: 1.5rem;
    }
    .navbar-toggler {
        border: 1px solid #028a0f;
    }
</style>
