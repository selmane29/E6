<?php
// Connexion à la base de données
$host = "localhost";
$user = "root";
$password = "";
$dbname = "informatique";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

$nom = "";
$description = "";
$prix = "";
$stock = "";
$image = "";
$script = "";
$message = "";
$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'] ?? '';
    $description = $_POST['description'] ?? '';
    $prix = $_POST['prix'] ?? '';
    $stock = $_POST['stock'] ?? '';
    $script = $_POST['script'] ?? '';

    if (!$nom || !$description || !$prix || !$stock) {
        $errorMessage = "Tous les champs obligatoires doivent être remplis.";
    } else {
        $image_path = "";

        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed = ["jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png"];
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
            $stmt = $conn->prepare("INSERT INTO produit (nom, description, prix, stock, image, script) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssdiss", $nom, $description, $prix, $stock, $image_path, $script);
            if ($stmt->execute()) {
                $message = "Produit ajouté avec succès";
                header("refresh:2;url=admin.php");
            } else {
                $errorMessage = "Erreur SQL : " . $conn->error;
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un produit</title>
    <style>
        * { box-sizing: border-box; font-family: Arial, sans-serif; }
        body { margin: 0; padding: 20px; background-color: #f5f5f5; }
        header { margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #ddd; }
        h1 { color: #333; }
        .form-container { background-color: white; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); max-width: 800px; margin: 0 auto; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="number"], textarea, input[type="file"] { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        textarea { height: 120px; }
        .buttons { margin-top: 20px; }
        .btn { padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; margin-right: 10px; }
        .btn-primary { background-color: #4CAF50; color: white; }
        .btn-secondary { background-color: #f0f0f0; color: #333; }
        .message { padding: 10px; margin: 20px 0; border-radius: 4px; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <?php include 'navbaradmin.php'; ?>
    <header><h1>Ajouter un produit</h1></header>
    <div class="form-container">
        <?php if (!empty($message)): ?><div class="message success"><?php echo $message; ?></div><?php endif; ?>
        <?php if (!empty($errorMessage)): ?><div class="message error"><?php echo $errorMessage; ?></div><?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nom">Nom du produit</label>
                <input type="text" id="nom" name="nom" required value="<?php echo htmlspecialchars($nom); ?>">
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description"><?php echo htmlspecialchars($description); ?></textarea>
            </div>
            <div class="form-group">
                <label for="prix">Prix (€)</label>
                <input type="number" id="prix" name="prix" step="0.01" required value="<?php echo htmlspecialchars($prix); ?>">
            </div>
            <div class="form-group">
                <label for="stock">Stock</label>
                <input type="number" id="stock" name="stock" required value="<?php echo htmlspecialchars($stock); ?>">
            </div>
            <div class="form-group">
                <label for="image">Image (fichier)</label>
                <input type="file" id="image" name="image">
            </div>
            <div class="form-group">
                <label for="script">Script (description détaillée)</label>
                <textarea id="script" name="script"><?php echo htmlspecialchars($script); ?></textarea>
            </div>
            <div class="buttons">
                <button type="submit" class="btn btn-primary">Ajouter</button>
                <a href="admin.php" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</body>
</html>







