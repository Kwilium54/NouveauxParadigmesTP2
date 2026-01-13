<?php
/**
 * Catalogue de produits
 */

use MongoDB\Client;

require_once __DIR__ . "/../src/vendor/autoload.php";

$client = new Client("mongodb://mongo");
$db = $client->chopizza;
$produits = $db->produits;

// Recup les catégories distinctes
$categories = $produits->distinct('categorie');

// Recup la catégorie sélectionnée (si existe)
$categorieSelectionnee = $_GET['categorie'] ?? null;

// Filtre
$filtre = $categorieSelectionnee ? ['categorie' => $categorieSelectionnee] : [];
$listeProduits = $produits->find($filtre, ['sort' => ['numero' => 1]]);

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Catalogue ChoPizza</title>
</head>
<body>
<pre>
═══════════════════════════════════════════════════════════════
                    CATALOGUE CHOPIZZA
═══════════════════════════════════════════════════════════════

Navigation par catégorie:
<?php
echo "<a href='catalogue.php'>[Toutes]</a> ";
foreach ($categories as $cat) {
    $active = ($cat === $categorieSelectionnee) ? '***' : '';
    echo "<a href='catalogue.php?categorie=" . urlencode($cat) . "'>[$cat]</a> $active ";
}
?>

<a href='ajout_produit.php' style='float:right;'>[+ Ajouter un produit]</a>

───────────────────────────────────────────────────────────────
<?php if ($categorieSelectionnee): ?>
Catégorie: <?= $categorieSelectionnee ?>
<?php else: ?>
Tous les produits
<?php endif; ?>
───────────────────────────────────────────────────────────────

<?php
$count = 0;
foreach ($listeProduits as $produit) {
    $count++;
    echo "\nN°{$produit['numero']} - {$produit['libelle']}\n";
    echo "{$produit['description']}\n";
    echo "Tarifs:\n";
    foreach ($produit['tarifs'] as $tarif) {
        printf("  - %-10s : %.2f €\n", ucfirst($tarif['taille']), $tarif['tarif']);
    }
    echo "───────────────────────────────────────────────────────────────\n";
}

if ($count === 0) {
    echo "\nAucun produit trouvé.\n";
} else {
    echo "\nTotal: $count produit(s)\n";
}
?>

═══════════════════════════════════════════════════════════════
</pre>
</body>
</html>
