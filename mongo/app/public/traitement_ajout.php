<?php
/**
 * Traitement du formulaire d'ajout de produit
 */

use MongoDB\Client;

require_once __DIR__ . "/../src/vendor/autoload.php";

$client = new Client("mongodb://mongo");
$db = $client->chopizza;
$produits = $db->produits;

$erreurs = [];
$succes = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Validation des champs obligatoires
    $numero = isset($_POST['numero']) ? (int) $_POST['numero'] : null;
    $libelle = isset($_POST['libelle']) ? trim($_POST['libelle']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $categorie = isset($_POST['categorie']) ? trim($_POST['categorie']) : '';
    $image = isset($_POST['image']) ? trim($_POST['image']) : '';
    if (!$numero) {
        $erreurs[] = "Le numéro est obligatoire";
    }
    if (empty($libelle)) {
        $erreurs[] = "Le libellé est obligatoire";
    }
    if (empty($description)) {
        $erreurs[] = "La description est obligatoire";
    }
    if (empty($categorie)) {
        $erreurs[] = "La catégorie est obligatoire";
    }

    $tarifs = [];
    if (!empty($_POST['taille1']) && isset($_POST['tarif1']) && $_POST['tarif1'] !== '') {
        $tarifs[] = [
            'taille' => $_POST['taille1'],
            'tarif' => (float) $_POST['tarif1']
        ];
    }
    if (!empty($_POST['taille2']) && isset($_POST['tarif2']) && $_POST['tarif2'] !== '') {
        $tarifs[] = [
            'taille' => $_POST['taille2'],
            'tarif' => (float) $_POST['tarif2']
        ];
    }
    if (empty($erreurs)) {
        $nouveauProduit = [
            'numero' => $numero,
            'libelle' => $libelle,
            'description' => $description,
            'image' => $image ?: 'https://via.placeholder.com/150',
            'categorie' => $categorie,
            'tarifs' => $tarifs,
            'recettes' => []
        ];

        try {
            $result = $produits->insertOne($nouveauProduit);
            $succes = true;
        } catch (Exception $e) {
            $erreurs[] = "Erreur lors de l'insertion : " . $e->getMessage();
        }
    }
}

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Résultat - ChoPizza</title>
</head>

<body>
    <pre>
═══════════════════════════════════════════════════════════════
                    RÉSULTAT DE L'AJOUT
═══════════════════════════════════════════════════════════════

<?php if ($succes): ?>
Produit ajouté avec succès !

Détails du produit :
      Numéro      : <?= $numero ?>

      Libellé     : <?= $libelle ?>

      Description : <?= $description ?>

      Catégorie   : <?= $categorie ?>

      Tarifs      : <?= count($tarifs) ?> tarif(s)

    <?php foreach ($tarifs as $t): ?>
            - <?= ucfirst($t['taille']) ?> : <?= number_format($t['tarif'], 2) ?> €
    <?php endforeach; ?>

    <a href='catalogue.php'>[Voir le catalogue]</a>
    <a href='ajout_produit.php'>[Ajouter un autre produit]</a>

<?php else: ?>
Erreur(s) lors de l'ajout :

<?php foreach ($erreurs as $err): ?>
          - <?= $err ?>

    <?php endforeach; ?>

    <a href='ajout_produit.php'>[&lt; Retour au formulaire]</a>

<?php endif; ?>

═══════════════════════════════════════════════════════════════
</pre>
</body>

</html>