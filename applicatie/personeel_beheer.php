<?php
session_start();
require_once(__DIR__ . "/includes/db_connectie.php");
include(__DIR__ . "/includes/navigatie.php");
include(__DIR__ . "/includes/header.php");
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
// Verwijder personeelslid
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["verwijder_username"])) {
    $verwijder = $_POST["verwijder_username"];

    // Voorkom dat admin zichzelf verwijdert
    if ($verwijder === $_SESSION["username"]) {
        $fout = "Je kunt jezelf niet verwijderen.";
    } else {
        try {
            $stmt = $conn->prepare("DELETE FROM [User] WHERE username = ? AND role = 'Personnel'");
            $stmt->execute([$verwijder]);
            header("Location: personeel_beheer.php");
            exit();
        } catch (PDOException $e) {
            $fout = "Fout bij verwijderen: " . $e->getMessage();
        }
    }
}

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
            <th>Verwijderen</th>
        </tr>
        <?php foreach ($gebruikers as $user): ?>
        <tr>
            <td><?= htmlspecialchars($user['username']) ?></td>
            <td><?= htmlspecialchars($user['first_name']) ?></td>
            <td><?= htmlspecialchars($user['last_name']) ?></td>
            <td>
                <form method="post" onsubmit="return confirm('Weet je zeker dat je <?= htmlspecialchars($user['username']) ?> wilt verwijderen?');">
                    <input type="hidden" name="verwijder_username" value="<?= htmlspecialchars($user['username']) ?>">
                    <button type="submit">Verwijderen</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

<br>
<a href="registratie_personeel.php">Personeelslid toevoegen</a><br>
<a href="personeel_bestellingen.php">Terug naar bestellingen</a>
