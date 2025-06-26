<?php
session_start();
require_once(__DIR__ . "/includes/db_connectie.php");
include(__DIR__ . "/includes/navigatie.php");
include(__DIR__ . "/includes/header.php");
// Alleen toegankelijk voor klanten
if (!isset($_SESSION["username"]) || strtolower($_SESSION["role"]) !== "client") {
    header("Location: login.php");
    exit();
}

$conn = maakVerbinding();
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$fout = "";

// Haal bestelling op voor deze gebruiker
try {
    $stmt = $conn->prepare("
        SELECT po.client_name, po.address, po.datetime, po.status,
               pop.product_name, pop.quantity
        FROM Pizza_Order po
        JOIN Pizza_Order_Product pop ON po.order_id = pop.order_id
        WHERE po.order_id = ? AND po.client_username = ?
    ");
    $stmt->execute([$order_id, $_SESSION["username"]]);
    $gegevens = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($gegevens)) {
        $fout = "Deze bestelling bestaat niet of is niet van jou.";
    } else {
        $eerste = $gegevens[0];
    }
} catch (PDOException $e) {
    $fout = "Fout bij ophalen bestelling: " . $e->getMessage();
}
?>

<h2>Bestelling #<?= htmlspecialchars($order_id) ?> – Details</h2>

<?php if ($fout): ?>
<p style="color:red;"><?= htmlspecialchars($fout) ?></p>
<?php elseif (!empty($gegevens)): ?>

<p><strong>Naam:</strong> <?= htmlspecialchars($eerste['client_name']) ?><br>
<strong>Adres:</strong> <?= htmlspecialchars($eerste['address']) ?><br>
<strong>Datum/tijd:</strong> <?= $eerste['datetime'] ?><br>
<strong>Status:</strong>
<?php
switch ($eerste['status']) {
    case 1: echo "Nieuw"; break;
    case 2: echo "In de oven"; break;
    case 3: echo "Bezorgd"; break;
    default: echo "Onbekend";
}
?>
</p>

<h3>Producten:</h3>
<ul>
<?php foreach ($gegevens as $rij): ?>
    <li><?= htmlspecialchars($rij['product_name']) ?> × <?= $rij['quantity'] ?></li>
<?php endforeach; ?>
</ul>

<a href="profiel.php">Terug naar overzicht</a>

<?php endif; ?>
