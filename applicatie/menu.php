<?php
session_start();
require_once("db_connectie.php");
include("navigatie.php");
// Alleen ingelogde klanten
if (!isset($_SESSION["username"]) || strtolower($_SESSION["role"]) !== "client") {
    header("Location: login.php");
    exit();
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

// Haal alle producten op uit database
try {
    $conn = maakVerbinding();

    $stmt = $conn->query("
        SELECT p.name, p.price, pt.name AS type
        FROM Product p
        JOIN ProductType pt ON p.type_id = pt.name
        ORDER BY pt.name, p.name
    ");
    $producten = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Fout bij ophalen producten: " . $e->getMessage());
}
?>

<h2>Menu</h2>

<?php if (!empty($message)): ?>
<p style="color:green"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<a href="winkelmandje.php">🛒 Naar winkelmandje</a><br><br>

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
    <strong><?= htmlspecialchars($product['name']) ?></strong> – €<?= number_format($product['price'], 2) ?>
    <input type="hidden" name="product_name" value="<?= htmlspecialchars($product['name']) ?>">
    <button type="submit">Toevoegen</button>
</form><br>

<?php endforeach; ?>
