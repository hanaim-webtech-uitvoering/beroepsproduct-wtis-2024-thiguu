<?php
require_once(__DIR__ . "/includes/db_connectie.php");

// Sessie starten als die nog niet actief is (al gedaan door session_start bovenaan, dus eigenlijk overbodig hier)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Voeg item toe aan winkelmandje
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["product_name"])) {
    $product_name = $_POST["product_name"];

    if (!isset($_SESSION["cart"])) {
        $_SESSION["cart"] = [];
    }

    if (!isset($_SESSION["cart"][$product_name])) {
        $_SESSION["cart"][$product_name] = 1;
    } else {
        $_SESSION["cart"][$product_name]++;
    }

    $message = "$product_name toegevoegd aan winkelmandje.";
}

// Haal producten op
try {
    $conn = maakVerbinding();

    // Haal producten met type op
    $stmt = $conn->query("
        SELECT p.name, p.price, pt.name AS type
        FROM Product p
        JOIN ProductType pt ON p.type_id = pt.name
        ORDER BY pt.name, p.name
    ");
    $producten = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Haal ingrediënten per product op
    $stmt = $conn->query("
        SELECT pi.product_name, i.name AS ingredient_name
        FROM Product_Ingredient pi
        JOIN Ingredient i ON pi.ingredient_name = i.name
        ORDER BY pi.product_name, i.name
    ");
    $ingredienten_resultaat = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Bouw array met ingrediënten per product
    $ingredienten_per_product = [];
    foreach ($ingredienten_resultaat as $rij) {
        $ingredienten_per_product[$rij["product_name"]][] = $rij["ingredient_name"];
    }

} catch (PDOException $e) {
    die("Fout bij ophalen producten: " . $e->getMessage());
}
?>
<?php
include(__DIR__ . "/includes/header.php");
include(__DIR__ . "/includes/navigatie.php");
?>
<h2>Menu</h2>

<?php if (!empty($message)): ?>
    <p style="color:green"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<a href="winkelmandje.php">Naar winkelmandje</a><br><br>

<?php
$huidige_type = null;
foreach ($producten as $product):
    if ($product['type'] !== $huidige_type):
        if ($huidige_type !== null) echo "<hr>";
        echo "<h3>" . htmlspecialchars($product['type']) . "</h3>";
        $huidige_type = $product['type'];
    endif;
?>

<form method="post" style="display:inline-block; margin-bottom: 10px;">
    <strong><?= htmlspecialchars($product['name']) ?></strong> – €<?= number_format($product['price'], 2) ?><br>

    <?php if (isset($ingredienten_per_product[$product['name']])): ?>
    <p style="margin: 5px 0; font-size: 0.9em;">
        <strong>Ingrediënten:</strong>
        <?= implode(", ", array_map("htmlspecialchars", $ingredienten_per_product[$product['name']])) ?>
    </p>
<?php endif; ?>


    <input type="hidden" name="product_name" value="<?= htmlspecialchars($product['name']) ?>">
    <button type="submit">Toevoegen</button>
</form><br>

<?php endforeach; ?>
