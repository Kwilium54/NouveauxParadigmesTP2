// Requêtes MongoDB - TD2 chopizza

// 1. Liste des produits
print("\n───────────────────────────────────────────────────────────────");
print("1. Liste des produits");
print("───────────────────────────────────────────────────────────────");
db.produits.find().limit(3).forEach(printjson);
print(`Total: ${db.produits.countDocuments()} produits`);

// 2. Compter les produits
print("\n───────────────────────────────────────────────────────────────");
print("2. Compter les produits");
print("───────────────────────────────────────────────────────────────");
print(`Nombre de produits: ${db.produits.countDocuments()}`);

// 3. Lister les produits en les triant par numero décroissant
print("\n───────────────────────────────────────────────────────────────");
print("3. Produits triés par numéro décroissant");
print("───────────────────────────────────────────────────────────────");
db.produits.find().sort({numero: -1}).limit(3).forEach(printjson);

// 4. Le produit de libellé "Margherita"
print("\n───────────────────────────────────────────────────────────────");
print("4. Produit 'Margherita'");
print("───────────────────────────────────────────────────────────────");
printjson(db.produits.findOne({libelle: "Margherita"}));

// 5. Produits de la catégorie "Boissons"
print("\n───────────────────────────────────────────────────────────────");
print("5. Produits de la catégorie 'Boissons'");
print("───────────────────────────────────────────────────────────────");
db.produits.find({categorie: "Boissons"}).limit(3).forEach(printjson);
print(`Total: ${db.produits.countDocuments({categorie: "Boissons"})} boissons`);

// 6. Liste des produits, afficher categorie, numero, libelle
print("\n───────────────────────────────────────────────────────────────");
print("6. Produits (categorie, numero, libelle uniquement)");
print("───────────────────────────────────────────────────────────────");
db.produits.find({}, {categorie: 1, numero: 1, libelle: 1, _id: 0}).limit(3).forEach(printjson);

// 7. Idem avec en plus la taille et le tarif
print("\n───────────────────────────────────────────────────────────────");
print("7. Produits avec taille et tarif (dénormalisé)");
print("───────────────────────────────────────────────────────────────");
db.produits.aggregate([
  {$unwind: "$tarifs"},
  {$project: {
    _id: 0,
    categorie: 1,
    numero: 1,
    libelle: 1,
    taille: "$tarifs.taille",
    tarif: "$tarifs.tarif"
  }},
  {$limit: 3}
]).forEach(printjson);

// 8. Produits avec un tarif < 8.0
print("\n───────────────────────────────────────────────────────────────");
print("8. Produits avec un tarif < 8.0");
print("───────────────────────────────────────────────────────────────");
db.produits.find({"tarifs.tarif": {$lt: 8.0}}).limit(3).forEach(printjson);
print(`Total: ${db.produits.countDocuments({"tarifs.tarif": {$lt: 8.0}})} produits`);

// 9. Produits avec un tarif grande taille < 8.0
print("\n───────────────────────────────────────────────────────────────");
print("9. Produits avec tarif grande taille < 8.0");
print("───────────────────────────────────────────────────────────────");
db.produits.find({
  tarifs: {
    $elemMatch: {
      taille: "grande",
      tarif: {$lt: 8.0}
    }
  }
}).limit(3).forEach(printjson);
print(`Total: ${db.produits.countDocuments({tarifs: {$elemMatch: {taille: "grande", tarif: {$lt: 8.0}}}})} produits`);

// 10. Insérer un nouveau produit
print("\n───────────────────────────────────────────────────────────────");
print("10. Insertion d'un nouveau produit");
print("───────────────────────────────────────────────────────────────");
var nouveauProduit = {
  numero: 99,
  libelle: "4 Fromages",
  description: "Tomate, mozzarella, gorgonzola, emmental, parmesan",
  image: "https://www.dominos.fr/ManagedAssets/FR/product/PZ4F.png",
  categorie: "Pizzas",
  tarifs: [
    {taille: "normale", tarif: 10.99},
    {taille: "grande", tarif: 13.99}
  ],
  recettes: []
};
var result = db.produits.insertOne(nouveauProduit);
print(`Produit inséré avec _id: ${result.insertedId}`);
printjson(db.produits.findOne({numero: 99}));

// 11. Les recettes associées au produit 1
print("\n───────────────────────────────────────────────────────────────");
print("11. Recettes associées au produit numéro 1");
print("───────────────────────────────────────────────────────────────");

// Étape 1 : Récupérer le produit 1
var produit1 = db.produits.findOne({numero: 1});
print(`Produit trouvé: ${produit1.libelle}`);
print(`IDs des recettes: ${produit1.recettes.length} recettes`);

// Étape 2 : Récupérer les recettes correspondantes
print("\nRecettes:");
db.recettes.find({_id: {$in: produit1.recettes}}).forEach(printjson);

print("\n═══════════════════════════════════════════════════════════════");
print("FIN DES REQUÊTES");
print("═══════════════════════════════════════════════════════════════\n");
