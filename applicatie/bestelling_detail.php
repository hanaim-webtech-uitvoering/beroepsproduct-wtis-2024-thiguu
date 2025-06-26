<?php
session_start();
require_once("db_connectie.php");
include("navigatie.php");
include("header.php");

// Alleen toegankelijk voor personeel
if (!isset($_SESSION["username"]) || strtolower($_SESSION["role"]) !== "personnel") {
    header("Location: login.php");
    exit();
}

$conn = maakVerbinding();
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$fout = "";
$melding = "";

// Status bijwerken
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_status"])) {
    $nieuwe_status = (int)$_POST["status"];
    try {
        $stmt = $conn->prepare("UPDATE Pizza_Order SET status = ? WHERE order_id = ?");
        $stmt->execute([$nieuwe_status, $order_id]);
        $melding = "Status bijgewerkt.";
    } catch (PDOException $e) {
        $fout = "Fout bij bijwerken: " . $e->getMessage();
    }
}

// Haal bestelling op
try {
    $stmt = $conn->prepare("
        SELECT po.client_name, po.address, po.datetime, po.status,
               pop.product_name, pop.quantity
        FROM Pizza_Order po
        JOIN Pizza_Order_Product pop ON po.order_id = pop.order_id
        WHERE po.order_id = ?
    ");
    $stmt->execute([$order_id]);
    $gegevens = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($gegevens)) {
        $fout = "Bestelling niet gevonden.";
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

<?php if ($melding): ?>
<p style="color:green;"><?= htmlspecialchars($melding) ?></p>
<?php endif; ?>

<p><strong>Klant:</strong> <?= htmlspecialchars($eerste['client_name']) ?><br>
<strong>Adres:</strong> <?= htmlspecialchars($eerste['address']) ?><br>
<strong>Datum/tijd:</strong> <?= $eerste['datetime'] ?><br></p>

<form method="post">
    <label>Status:</label>
    <select name="status">
        <option value="1" <?= $eerste['status'] == 1 ? 'selected' : '' ?>>Nieuw</option>
        <option value="2" <?= $eerste['status'] == 2 ? 'selected' : '' ?>>In de oven</option>
        <option value="3" <?= $eerste['status'] == 3 ? 'selected' : '' ?>>Bezorgd</option>
    </select>
    <button type="submit" name="update_status">Bijwerken</button>
</form>

<h3>Producten:</h3>
<ul>
<?php foreach ($gegevens as $rij): ?>
    <li><?= htmlspecialchars($rij['product_name']) ?> × <?= $rij['quantity'] ?></li>
<?php endforeach; ?>
</ul>

<a href="personeel_bestellingen.php">⬅ Terug naar overzicht</a>

<?php endif; ?>
