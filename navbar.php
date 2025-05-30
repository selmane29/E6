<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar navbar-expand-lg fixed-top" style="background-color: #0D6EFD;">
    <div class="container-fluid">
        <a class="navbar-brand" href="Stage.php">Informatique.net</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="Stage.php">Accueil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="profil.php">Profil</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-white" href="favoris.php">Mes Favoris</a>
                </li>


                <li class="nav-item d-flex align-items-center">
                    <a class="nav-link position-relative" href="mon_panier.php">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="white" class="bi bi-cart" viewBox="0 0 16 16">
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

            <!-- Barre de recherche stylisée -->
            <form class="d-flex me-3" action="search.php" method="get" style="max-width: 300px; width: 100%;">
                <input type="text" name="query" placeholder="Rechercher un produit"
                    class="form-control" required
                    style="border: 1px solid #ccc; border-right: none; border-radius: 4px 0 0 4px;">
                <button type="submit" class="btn"
                    style="background-color: #232F3E; border: none; border-radius: 0 4px 4px 0; padding: 6px 10px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="white" stroke-width="2" viewBox="0 0 16 16">
                        <circle cx="6.5" cy="6.5" r="5.5"/>
                        <line x1="11" y1="11" x2="15" y2="15" />
                    </svg>
                </button>
            </form>

            <!-- Connexion utilisateur -->
            <?php if (isset($_SESSION['email'])): ?>
                <div class="d-flex align-items-center text-white fw-bold">
                    👋 Bonjour, <?= htmlspecialchars($_SESSION['email']) ?> (<?= htmlspecialchars($_SESSION['role']) ?>)
                    <a href="logout.php" class="btn btn-outline-light btn-sm ms-2">Déconnexion</a>
                </div>
            <?php else: ?>
                <a href="connection.php" class="btn btn-light btn-sm">Connexion</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<style>
    body {
        padding-top: 70px;
    }

    .navbar .nav-link,
    .navbar .navbar-brand {
        color: white !important;
        font-weight: bold;
        transition: color 0.3s ease-in-out;
    }

    .navbar .nav-link:hover,
    .navbar .navbar-brand:hover {
        color: #e6e6e6 !important;
    }

    .navbar svg {
        fill: white !important;
    }

    .navbar-toggler {
        border: 1px solid white;
    }

    .navbar-toggler-icon {
        background-image: url("data:image/svg+xml;charset=utf8,%3Csvg viewBox='0 0 30 30' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath stroke='white' stroke-width='2' stroke-linecap='round' stroke-miterlimit='10' d='M4 7h22M4 15h22M4 23h22'/%3E%3C/svg%3E");
    }

    .form-control:focus {
        box-shadow: 0 0 5px #0D6EFD;
        border: 1px solid #0D6EFD;
    }
</style>
