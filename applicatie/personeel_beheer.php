<?php
session_start();
require_once("db_connectie.php");

// Alleen voor ingelogde personeelsleden
if (!isset($_SESSION["username"]) || strtolower($_SESSION["role"]) !== "personnel") {
    header("Location: login.php");
    exit();
}

$conn = maakVerbinding();
// Extra admin-check
$stmt = $conn->prepare("SELECT is_admin FROM [User] WHERE username = ?");
$stmt->execute([$_SESSION["username"]]);
$user = $stmt->fetch();

if (!$user || !$user["is_admin"]) {
    die("Geen toegang, alleen voor beheerders.");
}

$fout = "";

// Haal alle personeelsleden op
try {
    $stmt = $conn->prepare("SELECT username, first_name, last_name FROM [User] WHERE role = ?");
    $stmt->execute(['Personnel']);
    $gebruikers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $fout = "Fout bij ophalen personeelsleden: " . $e->getMessage();
}
?>

<h2>Overzicht personeelsleden</h2>

<?php if ($fout): ?>
    <p style="color:red;"><?= htmlspecialchars($fout) ?></p>
<?php elseif (empty($gebruikers)): ?>
    <p>Er zijn nog geen personeelsleden geregistreerd.</p>
<?php else: ?>
    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>Gebruikersnaam</th>
            <th>Voornaam</th>
            <th>Achternaam</th>
        </tr>
        <?php foreach ($gebruikers as $user): ?>
        <tr>
            <td><?= htmlspecialchars($user['username']) ?></td>
            <td><?= htmlspecialchars($user['first_name']) ?></td>
            <td><?= htmlspecialchars($user['last_name']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

<br>
<a href="registratie_personeel.php">➕ Personeelslid toevoegen</a><br>
<a href="personeel_bestellingen.php">⬅ Terug naar bestellingen</a>
