<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar navbar-expand-lg fixed-top" style="background-color: #ffffff; border-bottom: 2px solid #028a0f;">
    <div class="container-fluid">
        <a class="navbar-brand" href="Stage.php" style="color: #028a0f; font-weight: bold;">Informatique.net</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon" style="color: #028a0f;"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="Stage.php" style="color: #028a0f;">Accueil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="profil.php" style="color: #028a0f;">profil</a>
                </li>

                <li class="nav-item d-flex align-items-center">
    <a class="nav-link position-relative" href="mon_panier.php" style="color: #028a0f;">
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

            <form class="d-flex me-3 position-relative" action="search.php" method="get" style="width: 250px;">
                <input type="text" name="query" placeholder="Recherche un produit..." 
                    class="form-control ps-5" 
                    style="border: 3px solid #028a0f; border-radius: 50px;" required>
                <button type="submit" class="btn position-absolute" style="top: 50%; left: 15px; transform: translateY(-50%); background: none; border: none; padding: 0;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#028a0f" class="bi bi-search" viewBox="0 0 16 16">
                    <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001l3.85 3.85a1 1 0 0 0 
                            1.415-1.414l-3.85-3.85zm-5.242 1.356a5.5 5.5 
                            0 1 1 0-11 5.5 5.5 0 0 1 0 11z"/>
                    </svg>
                </button>
            </form>

            <?php if (isset($_SESSION['username'])): ?>
                <div class="d-flex align-items-center" style="color: #028a0f; font-weight: bold;">
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
    body {
    padding-top: 70px;
    }

    .navbar .nav-link {
        color: #028a0f !important;
        font-weight: bold;
        transition: color 0.3s ease-in-out;
    }

    .navbar .nav-link:hover {
        color: #026f0c !important;
    }

    .navbar-brand {
        color: #028a0f !important;
        font-size: 1.5rem;
        transition: color 0.3s ease-in-out;
    }

    .navbar-brand:hover {
        color: #026f0c !important;
    }

    .search-icon {
        filter: invert(26%) sepia(72%) saturate(2422%) hue-rotate(111deg) brightness(89%) contrast(100%);
    }

    .search-icon:hover {
        filter: invert(38%) sepia(75%) saturate(2326%) hue-rotate(119deg) brightness(77%) contrast(111%);
    }

    .navbar-toggler {
        border: 1px solid #028a0f;
    }

    .navbar-toggler-icon {
        background-color: transparent;
        border-radius: 5px;
    }

    .form-control:focus {   
        box-shadow: 0 0 5px #028a0f;
        border: 1px solid #026f0c;
    }
</style>
