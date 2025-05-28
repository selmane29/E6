<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar navbar-expand-lg fixed-top" style="background-color: #ffffff; border-bottom: 2px solid #0D6EFD;">
    <div class="container-fluid">
        <a class="navbar-brand" href="Stage.php" style="color: #0D6EFD; font-weight: bold;">Informatique.net</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon" style="color: #0D6EFD;"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="Stage.php" style="color: #0D6EFD;">Accueil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="profil.php" style="color: #0D6EFD;">profil</a>
                </li>
                <li class="nav-item d-flex align-items-center">
                    <a class="nav-link position-relative" href="mon_panier.php" style="color: #0D6EFD;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#0D6EFD" class="bi bi-cart" viewBox="0 0 16 16">
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
                    style="background-color: #0B5ED7; border: none; border-radius: 0 4px 4px 0; padding: 6px 10px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="white" stroke-width="2" viewBox="0 0 16 16">
                        <circle cx="6.5" cy="6.5" r="5.5"/>
                        <line x1="11" y1="11" x2="15" y2="15" />
                    </svg>
                </button>
            </form>

            <!-- Connexion utilisateur -->
            <?php if (isset($_SESSION['email'])): ?>
                <div class="d-flex align-items-center" style="color: #0D6EFD; font-weight: bold;">
                    👋 Bonjour, <?= htmlspecialchars($_SESSION['email']) ?> (<?= htmlspecialchars($_SESSION['role']) ?>)
                    <a href="logout.php" class="btn btn-outline-primary btn-sm ms-2">Déconnexion</a>
                </div>
            <?php else: ?>
                <a href="connection.php" class="btn btn-primary btn-sm">Connexion</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<style>
    body {
        padding-top: 70px;
    }

    .navbar .nav-link {
        color: #0D6EFD !important;
        font-weight: bold;
        transition: color 0.3s ease-in-out;
    }

    .navbar .nav-link:hover {
        color: #0D6EFD !important;
    }

    .navbar-brand {
        color: #0D6EFD !important;
        font-size: 1.5rem;
        transition: color 0.3s ease-in-out;
    }

    .navbar-brand:hover {
        color: #0D6EFD !important;
    }

    .navbar-toggler {
        border: 1px solid #0D6EFD;
    }

    .navbar-toggler-icon {
        background-color: transparent;
        border-radius: 5px;
    }

    .form-control:focus {
        box-shadow: 0 0 5px #0D6EFD;
        border: 1px solid #0D6EFD;
    }
</style>
