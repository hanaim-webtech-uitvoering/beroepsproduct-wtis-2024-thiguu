<?php
session_start();
require_once("db_connectie.php");
include("navigatie.php");
if (!isset($_SESSION["username"]) || strtolower($_SESSION["role"]) !== "client") {
    header("Location: login.php");
    exit();
}

$conn = maakVerbinding();
$cart = $_SESSION["cart"] ?? [];
$adres = "";
$fout = "";
$gelukt = false;

// 🔄 Verwijder product
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["verwijder"])) {
    $product = $_POST["verwijder"];
    unset($_SESSION["cart"][$product]);
    header("Location: winkelmandje.php");
    exit();
}

// 🔁 Aantal bijwerken
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update"])) {
    foreach ($_POST["aantal"] as $product => $value) {
        $waarde = max(1, (int)$value);
        $_SESSION["cart"][$product] = $waarde;
    }
    header("Location: winkelmandje.php");
    exit();
}

// ✅ Bestelling plaatsen
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["plaats_bestelling"])) {
    $adres = trim($_POST["adres"]);
    if (empty($cart)) {
        $fout = "Je winkelmandje is leeg.";
    } elseif (empty($adres)) {
        $fout = "Voer een afleveradres in.";
    } else {
        try {
            $conn->beginTransaction();

            $sqlOrder = "INSERT INTO Pizza_Order (client_username, client_name, personnel_username, datetime, status, address)
                         VALUES (?, ?, ?, GETDATE(), ?, ?)";
            $stmt = $conn->prepare($sqlOrder);
            $stmt->execute([
                $_SESSION["username"],
                $_SESSION["username"],
                "rdeboer", // placeholder
                1,
                $adres
            ]);

            $order_id = $conn->lastInsertId();

            $sqlItem = "INSERT INTO Pizza_Order_Product (order_id, product_name, quantity) VALUES (?, ?, ?)";
            $stmtItem = $conn->prepare($sqlItem);

            foreach ($_SESSION["cart"] as $product => $aantal) {
                $stmtItem->execute([$order_id, $product, $aantal]);
            }

            $conn->commit();
            $_SESSION["cart"] = [];
            $gelukt = true;
        } catch (PDOException $e) {
            $conn->rollBack();
            $fout = "Fout bij bestelling: " . $e->getMessage();
        }
    }
}
?>

<h2>Winkelmandje</h2>

<?php if ($gelukt): ?>
    <p style="color:green;">Bestelling succesvol geplaatst!</p>
    <a href="menu.php">Terug naar menu</a>
    <?php exit(); ?>
<?php endif; ?>

<?php if (!empty($fout)): ?>
    <p style="color:red;"><?= htmlspecialchars($fout) ?></p>
<?php endif; ?>

<?php if (empty($_SESSION["cart"])): ?>
    <p>Je winkelmandje is leeg.</p>
    <a href="menu.php">⬅ Terug naar menu</a>
<?php else: ?>
    <form method="post">
    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>Product</th>
            <th>Prijs</th>
            <th>Aantal</th>
            <th>Subtotaal</th>
            <th>Actie</th>
        </tr>
        <?php
        $totaal = 0;
        $stmt = $conn->prepare("SELECT name, price FROM Product WHERE name = ?");
        foreach ($_SESSION["cart"] as $product => $aantal):
            $stmt->execute([$product]);
            $row = $stmt->fetch();
            $subtotaal = $row["price"] * $aantal;
            $totaal += $subtotaal;
        ?>
        <tr>
            <td><?= htmlspecialchars($product) ?></td>
            <td>€<?= number_format($row["price"], 2) ?></td>
            <td>
                <input type="number" name="aantal[<?= htmlspecialchars($product) ?>]" value="<?= $aantal ?>" min="1" style="width:60px;">
            </td>
            <td>€<?= number_format($subtotaal, 2) ?></td>
            <td>
                <button type="submit" name="verwijder" value="<?= htmlspecialchars($product) ?>">Verwijderen</button>
            </td>
        </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="3" align="right"><strong>Totaal:</strong></td>
            <td colspan="2"><strong>€<?= number_format($totaal, 2) ?></strong></td>
        </tr>
    </table>

    <br>
    <input type="submit" name="update" value="Aantallen bijwerken">
    </form>

    <br><br>

    <form method="post">
        <label>Afleveradres:</label><br>
        <input type="text" name="adres" value="<?= htmlspecialchars($adres) ?>" required style="width:300px;"><br><br>
        <input type="submit" name="plaats_bestelling" value="Bestelling plaatsen">
    </form>

    <br><a href="menu.php">⬅ Verder winkelen</a>
<?php endif; ?>
