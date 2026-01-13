<?php
/**
 * Formulaire d'ajout d'un produit
 */

use MongoDB\Client;

require_once __DIR__ . "/../src/vendor/autoload.php";

$client = new Client("mongodb://mongo");
$db = $client->chopizza;
$produits = $db->produits;

// Recup les catégories existantes
$categories = $produits->distinct('categorie');
$tailles = ['normale', 'grande'];

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Ajouter un produit - ChoPizza</title>
</head>
<body>
<pre>
═══════════════════════════════════════════════════════════════
                    AJOUTER UN PRODUIT
═══════════════════════════════════════════════════════════════

<a href='catalogue.php'>[&lt; Retour au catalogue]</a>

───────────────────────────────────────────────────────────────
</pre>

<form method="POST" action="traitement_ajout.php" style="max-width: 600px; margin: 20px;">
    <table>
        <tr>
            <td><strong>Numéro *</strong></td>
            <td><input type="number" name="numero" required style="width: 200px;"></td>
        </tr>
        <tr>
            <td><strong>Libellé *</strong></td>
            <td><input type="text" name="libelle" required style="width: 300px;"></td>
        </tr>
        <tr>
            <td><strong>Description *</strong></td>
            <td><textarea name="description" required rows="3" style="width: 300px;"></textarea></td>
        </tr>
        <tr>
            <td><strong>Image (URL)</strong></td>
            <td><input type="text" name="image" style="width: 400px;" placeholder="https://..."></td>
        </tr>
        <tr>
            <td><strong>Catégorie *</strong></td>
            <td>
                <select name="categorie" required style="width: 200px;">
                    <option value="">-- Sélectionner --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="2"><hr><strong>Tarifs</strong></td>
        </tr>
        <tr>
            <td><strong>Taille 1</strong></td>
            <td>
                <select name="taille1" style="width: 120px;">
                    <option value="">-- Sélectionner --</option>
                    <?php foreach ($tailles as $t): ?>
                        <option value="<?= $t ?>"><?= ucfirst($t) ?></option>
                    <?php endforeach; ?>
                </select>
                Tarif: <input type="number" step="0.01" name="tarif1" style="width: 80px;"> €
            </td>
        </tr>
        <tr>
            <td><strong>Taille 2</strong></td>
            <td>
                <select name="taille2" style="width: 120px;">
                    <option value="">-- Sélectionner --</option>
                    <?php foreach ($tailles as $t): ?>
                        <option value="<?= $t ?>"><?= ucfirst($t) ?></option>
                    <?php endforeach; ?>
                </select>
                Tarif: <input type="number" step="0.01" name="tarif2" style="width: 80px;"> €
            </td>
        </tr>
        <tr>
            <td colspan="2"><hr></td>
        </tr>
        <tr>
            <td colspan="2">
                <button type="submit" style="padding: 10px 20px; font-size: 14px;">Ajouter le produit</button>
                <button type="reset" style="padding: 10px 20px; font-size: 14px;">Réinitialiser</button>
            </td>
        </tr>
    </table>
</form>

<pre>
───────────────────────────────────────────────────────────────
* Champs obligatoires
═══════════════════════════════════════════════════════════════
</pre>

</body>
</html>
