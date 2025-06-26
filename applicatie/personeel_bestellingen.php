<?php
session_start();
require_once("db_connectie.php");
include("navigatie.php");
include("header.php");
if (!isset($_SESSION["username"]) || strtolower($_SESSION["role"]) !== "personnel") {
    header("Location: login.php");
    exit();
}

$conn = maakVerbinding();
$fout = "";
$melding = "";

// Status bijwerken
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_status"])) {
    $order_id = (int)$_POST["order_id"];
    $nieuwe_status = (int)$_POST["status"];

    try {
        $stmt = $conn->prepare("UPDATE Pizza_Order SET status = ? WHERE order_id = ?");
        $stmt->execute([$nieuwe_status, $order_id]);
        $melding = "Status van bestelling $order_id bijgewerkt.";
    } catch (PDOException $e) {
        $fout = "Fout bij bijwerken: " . $e->getMessage();
    }
}

// Bestellingen ophalen
try {
    $stmt = $conn->query("
    SELECT po.order_id, po.client_name, po.address, po.datetime, po.status,
           pop.product_name, pop.quantity
    FROM Pizza_Order po
    JOIN Pizza_Order_Product pop ON po.order_id = pop.order_id
    ORDER BY po.order_id DESC
    ");


    $resultaten = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Groeperen bestellingen op order_id
    $bestellingen = [];
    foreach ($resultaten as $rij) {
        $id = $rij['order_id'];
        if (!isset($bestellingen[$id])) {
            $bestellingen[$id] = [
                'client_name' => $rij['client_name'],
                'address'     => $rij['address'],
                'datetime'    => $rij['datetime'],
                'status'      => $rij['status'],
                'producten'   => []
            ];
        }
        $bestellingen[$id]['producten'][] = [
            'naam'    => $rij['product_name'],
            'aantal'  => $rij['quantity']
        ];
    }
} catch (PDOException $e) {
    die("Fout bij ophalen bestellingen: " . $e->getMessage());
}
?>

<h2>Bestellingsoverzicht voor personeel</h2>

<?php if ($melding): ?>
<p style="color:green;"><?= htmlspecialchars($melding) ?></p>
<?php endif; ?>

<?php if ($fout): ?>
<p style="color:red;"><?= htmlspecialchars($fout) ?></p>
<?php endif; ?>

<?php if (empty($bestellingen)): ?>
<p>Er zijn momenteel geen bestellingen.</p>
<?php else: ?>
<?php foreach ($bestellingen as $id => $gegevens): ?>
<hr>
<h3>Bestelling #<?= $id ?></h3>
<p><strong>Klant:</strong> <?= htmlspecialchars($gegevens['client_name'] ?? 'Gast') ?><br>
<strong>Adres:</strong> <?= htmlspecialchars($gegevens['address']) ?><br>
<strong>Datum/tijd:</strong> <?= $gegevens['datetime'] ?><br>

<form method="post" style="margin-top: 5px;">
    <input type="hidden" name="order_id" value="<?= $id ?>">
    <label>Status:</label>
    <select name="status">
        <option value="1" <?= $gegevens['status'] == 1 ? 'selected' : '' ?>>Nieuw</option>
        <option value="2" <?= $gegevens['status'] == 2 ? 'selected' : '' ?>>In de oven</option>
        <option value="3" <?= $gegevens['status'] == 3 ? 'selected' : '' ?>>Bezorgd</option>
    </select>
    <button type="submit" name="update_status">Bijwerken</button>
</form>

<p><strong>Producten:</strong></p>
<ul>
    <?php foreach ($gegevens['producten'] as $product): ?>
        <li><?= htmlspecialchars($product['naam']) ?> × <?= $product['aantal'] ?></li>
    <?php endforeach; ?>
</ul>
<?php endforeach; ?>
<?php endif; ?>
