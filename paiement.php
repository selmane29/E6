
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Paiement</title>
</head>
<body>
    <h2>Formulaire de paiement</h2>
    <form action="confirmation.php" method="POST">
        <fieldset>
            <legend>Informations personnelles</legend>
            <input type="text" name="nom" placeholder="Nom complet" required><br>
            <input type="email" name="email" placeholder="Adresse email" required><br>
        </fieldset>
        <fieldset>
            <legend>Adresse de livraison</legend>
            <input type="text" name="adresse" placeholder="Adresse" required><br>
            <input type="text" name="ville" placeholder="Ville" required><br>
            <input type="text" name="code_postal" placeholder="Code postal" required><br>
            <input type="text" name="pays" placeholder="Pays" required><br>
        </fieldset>
        <fieldset>
            <legend>Carte bancaire (simulation)</legend>
            <input type="text" name="carte_numero" placeholder="Numéro de carte" required><br>
            <input type="text" name="carte_exp" placeholder="MM/AA" required><br>
            <input type="text" name="carte_cvc" placeholder="CVC" required><br>
        </fieldset>
        <button type="submit">Payer</button>
    </form>
</body>
</html>
