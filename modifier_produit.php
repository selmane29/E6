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

$id = "";
$nom = "";
$description = "";
$prix = "";
$stock = "";
$image = "";
$script = "";
$message = "";
$errorMessage = "";

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM produit WHERE produit_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        $nom = $product['nom'];
        $description = $product['description'];
        $prix = $product['prix'];
        $stock = $product['stock'];
        $image = $product['image'];
        $script = $product['script'];
    } else {
        $errorMessage = "Produit non trouvé";
    }
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['produit_id'];
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $prix = $_POST['prix'];
    $stock = $_POST['stock'];
    $script = $_POST['script'];
    $image_path = $_POST['current_image'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png");
        $filename = $_FILES["image"]["name"];
        $filetype = $_FILES["image"]["type"];
        $filesize = $_FILES["image"]["size"];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        if (array_key_exists($ext, $allowed)) {
            $maxsize = 5 * 1024 * 1024;
            if ($filesize < $maxsize) {
                $new_filename = "img/" . uniqid() . "." . $ext;
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $new_filename)) {
                    $image_path = $new_filename;
                } else {
                    $errorMessage = "Erreur lors du téléchargement de l'image.";
                }
            } else {
                $errorMessage = "L'image est trop grande. Max 5MB.";
            }
        } else {
            $errorMessage = "Type de fichier non autorisé.";
        }
    }

    if (empty($errorMessage)) {
        $sql = "UPDATE produit SET nom = ?, description = ?, prix = ?, stock = ?, image = ?, script = ? WHERE produit_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdissi", $nom, $description, $prix, $stock, $image_path, $script, $id);

        if ($stmt->execute()) {
            $message = "Produit mis à jour avec succès";
            header("refresh:2;url=ajouter_produit.php");
        } else {
            $errorMessage = "Erreur lors de la mise à jour du produit: " . $conn->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un produit</title>
    <!-- Bootstrap CSS + JS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        * {
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            margin: 0;
            padding: 70px 20px 20px;
            background-color: #f5f5f5;
        }
        header {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        h1 {
            color: #333;
        }
        .form-container {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            max-width: 800px;
            margin: 0 auto;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="number"],
        textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        textarea {
            height: 120px;
        }
        .preview-image {
            max-width: 200px;
            max-height: 200px;
            margin-top: 10px;
            display: block;
        }
        .buttons {
            margin-top: 20px;
        }
        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-right: 10px;
        }
        .btn-primary {
            background-color: #4CAF50;
            color: white;
        }
        .btn-secondary {
            background-color: #f0f0f0;
            color: #333;
        }
        .message {
            padding: 10px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
<?php include 'navbaradmin.php'; ?>

<header>
    <h1 class="text-center">Modifier un produit</h1>
</header>

<div class="form-container">
    <?php if (!empty($message)): ?>
        <div class="message success"><?php echo $message; ?></div>
    <?php endif; ?>
    <?php if (!empty($errorMessage)): ?>
        <div class="message error"><?php echo $errorMessage; ?></div>
    <?php endif; ?>

    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $id); ?>" enctype="multipart/form-data">
        <input type="hidden" name="produit_id" value="<?php echo $id; ?>">
        <input type="hidden" name="current_image" value="<?php echo $image; ?>">

        <div class="form-group">
            <label for="nom">Nom du produit</label>
            <input type="text" id="nom" name="nom" value="<?php echo $nom; ?>" required>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description"><?php echo $description; ?></textarea>
        </div>
        <div class="form-group">
            <label for="prix">Prix (€)</label>
            <input type="number" id="prix" name="prix" step="0.01" value="<?php echo $prix; ?>" required>
        </div>
        <div class="form-group">
            <label for="stock">Stock</label>
            <input type="number" id="stock" name="stock" value="<?php echo $stock; ?>" required>
        </div>
        <div class="form-group">
            <label for="image">Image actuelle</label>
            <?php if (!empty($image)): ?>
                <img src="<?php echo $image; ?>" alt="<?php echo $nom; ?>" class="preview-image">
            <?php else: ?>
                <p>Aucune image</p>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="image">Nouvelle image</label>
            <input type="file" id="image" name="image">
        </div>
        <div class="form-group">
            <label for="script">Script</label>
            <textarea id="script" name="script"><?php echo $script; ?></textarea>
        </div>
        <div class="buttons">
            <button type="submit" class="btn btn-primary">Mettre à jour</button>
            <a href="stock.php" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>
</body>
</html>
