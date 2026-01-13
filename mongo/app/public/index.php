<?php
/**
 * Created by PhpStorm.
 * User: canals5
 * Date: 28/10/2019
 * Time: 16:16
 */

use MongoDB\Client;

require_once __DIR__ . "/../src/vendor/autoload.php";

$client = new Client("mongodb://mongo");
$db = $client->chopizza;
$produits = $db->produits;
$recettes = $db->recettes;

echo "<pre>";
echo "═══════════════════════════════════════════════════════════════\n";
echo "Requêtes MongoDB en PHP - Base chopizza\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

// 1. Afficher la liste des produits: numero, categorie, libelle
echo "───────────────────────────────────────────────────────────────\n";
echo "1. Liste des produits (numero, categorie, libelle)\n";
echo "───────────────────────────────────────────────────────────────\n";

$cursor = $produits->find([], [
    'projection' => ['numero' => 1, 'categorie' => 1, 'libelle' => 1, '_id' => 0],
    'sort' => ['numero' => 1],
    'limit' => 10
]);

foreach ($cursor as $produit) {
    printf("N°%d - %-15s - %s\n", 
        $produit['numero'], 
        $produit['categorie'], 
        $produit['libelle']
    );
}

// 2. Afficher le produit numéro 6: libellé, catégorie, description, tarifs
echo "\n───────────────────────────────────────────────────────────────\n";
echo "2. Produit numéro 6 (détails complets)\n";
echo "───────────────────────────────────────────────────────────────\n";

$produit6 = $produits->findOne(['numero' => 6]);

echo "Libellé: {$produit6['libelle']}\n";
echo "Catégorie: {$produit6['categorie']}\n";
echo "Description: {$produit6['description']}\n";
echo "Tarifs:\n";
foreach ($produit6['tarifs'] as $tarif) {
    printf("  - Taille %-10s : %.2f €\n", $tarif['taille'], $tarif['tarif']);
}

// 3. Liste des produits dont le tarif en taille normale est <= 3.0
echo "\n───────────────────────────────────────────────────────────────\n";
echo "3. Produits avec tarif taille normale <= 3.0 €\n";
echo "───────────────────────────────────────────────────────────────\n";

$cursor = $produits->find([
    'tarifs' => [
        '$elemMatch' => [
            'taille' => 'normale',
            'tarif' => ['$lte' => 3.0]
        ]
    ]
]);

$count = 0;
foreach ($cursor as $produit) {
    $tarifNormal = null;
    foreach ($produit['tarifs'] as $t) {
        if ($t['taille'] === 'normale') {
            $tarifNormal = $t['tarif'];
            break;
        }
    }
    printf("N°%d - %-20s - %.2f €\n", 
        $produit['numero'], 
        $produit['libelle'], 
        $tarifNormal
    );
    $count++;
}
echo "Total: $count produits trouvés\n";

// 4. Liste des produits associés à 4 recettes
echo "\n───────────────────────────────────────────────────────────────\n";
echo "4. Produits associés à exactement 4 recettes\n";
echo "───────────────────────────────────────────────────────────────\n";

$cursor = $produits->find([
    '$expr' => ['$eq' => [['$size' => '$recettes'], 4]]
]);

$count = 0;
foreach ($cursor as $produit) {
    printf("N°%d - %-20s - %d recettes\n", 
        $produit['numero'], 
        $produit['libelle'],
        count($produit['recettes'])
    );
    $count++;
}
echo "Total: $count produits trouvés\n";

// 5. Afficher le produit n°6, compléter en listant les recettes associées
echo "\n───────────────────────────────────────────────────────────────\n";
echo "5. Produit n°6 avec ses recettes (nom et difficulté)\n";
echo "───────────────────────────────────────────────────────────────\n";

$produit6 = $produits->findOne(['numero' => 6]);
echo "Produit: {$produit6['libelle']}\n";
echo "Recettes associées (" . count($produit6['recettes']) . "):\n";

$recettesAssociees = $recettes->find([
    '_id' => ['$in' => $produit6['recettes']]
]);

foreach ($recettesAssociees as $recette) {
    printf("  - %-30s (difficulté: %s)\n", 
        $recette['nom'], 
        $recette['difficulte']
    );
}

// 6. Fonction qui retourne les données d'un produit par numéro et taille
echo "\n───────────────────────────────────────────────────────────────\n";
echo "6. Fonction getProduitParNumeroTaille()\n";
echo "───────────────────────────────────────────────────────────────\n";

function getProduitParNumeroTaille($numero, $taille) {
    global $produits;
    
    $produit = $produits->findOne(['numero' => $numero]);
    if (!$produit) {
        return null;
    }
    $tarifTrouve = null;
    foreach ($produit['tarifs'] as $tarif) {
        if ($tarif['taille'] === $taille) {
            $tarifTrouve = $tarif['tarif'];
            break;
        }
    }
    if ($tarifTrouve === null) {
        return null;
    }
    return [
        'numero' => $produit['numero'],
        'libelle' => $produit['libelle'],
        'categorie' => $produit['categorie'],
        'taille' => $taille,
        'tarif' => $tarifTrouve
    ];
}

echo "Test: getProduitParNumeroTaille(6, 'grande')\n\n";
$resultat = getProduitParNumeroTaille(6, "grande");

if ($resultat) {
    echo "Résultat en JSON:\n";
    echo json_encode($resultat, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    echo "\n";
} else {
    echo "Aucun résultat trouvé.\n";
}
echo "</pre>";

