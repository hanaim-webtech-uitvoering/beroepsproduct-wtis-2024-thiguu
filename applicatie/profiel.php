<?php
session_start();
require_once("db_connectie.php");
include("navigatie.php");
// Alleen voor klanten
if (!isset($_SESSION["username"]) || strtolower($_SESSION["role"]) !== "client") {
    header("Location: login.php");
    exit();
}

$conn = maakVerbinding();
$fout = "";

// Bestellingen ophalen
try {
    $stmt = $conn->prepare("
        SELECT order_id, datetime, address, status
        FROM Pizza_Order
        WHERE client_username = ?
        ORDER BY datetime DESC
    ");
    $stmt->execute([$_SESSION["username"]]);
    $bestellingen = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $fout = "Fout bij ophalen bestellingen: " . $e->getMessage();
}
?>
<!-- css inladen -->
<link rel="stylesheet" href="style.css">
<!-- laten zien van bestellingen -->
<h2>Mijn bestellingen</h2>

<?php if ($fout): ?>
    <p style="color:red;"><?= htmlspecialchars($fout) ?></p>
<?php elseif (empty($bestellingen)): ?>
    <p>Je hebt nog geen bestellingen geplaatst.</p>
<?php else: ?>
    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>Datum</th>
            <th>Status</th>
            <th>Adres</th>
            <th>Details</th>
        </tr>
        <?php foreach ($bestellingen as $b): ?>
        <tr>
            <td><?= $b["datetime"] ?></td>
            <td>
                <?php
                switch ($b["status"]) {
                    case 1: echo "Nieuw"; break;
                    case 2: echo "In de oven"; break;
                    case 3: echo "Bezorgd"; break;
                    default: echo "Onbekend";
                }
                ?>
            </td>
            <td><?= htmlspecialchars($b["address"]) ?></td>
            <td><a href="bestelling_klant_detail.php?id=<?= $b["order_id"] ?>">Bekijk</a></td>
        </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>
