<?php
session_start();
require_once("db_connectie.php");
include("navigatie.php");
include("header.php");

// Alleen voor ingelogde beheerders
if (!isset($_SESSION["username"]) || strtolower($_SESSION["role"]) !== "personnel") {
    header("Location: login.php");
    exit();
}

$conn = maakVerbinding();

// Admin-verificatie
$stmt = $conn->prepare("SELECT is_admin FROM [User] WHERE username = ?");
$stmt->execute([$_SESSION["username"]]);
$user = $stmt->fetch();

if (!$user || !$user["is_admin"]) {
    die("Geen toegang – alleen voor beheerders.");
}

$fout = "";
$melding = "";

// Variabele aanmaken nieuw personeel
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username   = trim($_POST["username"]);
    $password   = trim($_POST["password"]);
    $first_name = trim($_POST["first_name"]);
    $last_name  = trim($_POST["last_name"]);
    $is_admin   = isset($_POST["is_admin"]) ? 1 : 0;

    if (!$username || !$password || !$first_name || !$last_name) {
        $fout = "Alle velden zijn verplicht.";
    } else {
        try {
            // Controleer of gebruikersnaam al bestaat
            $check = $conn->prepare("SELECT username FROM [User] WHERE username = ?");
            $check->execute([$username]);

            if ($check->fetch()) {
                $fout = "Gebruikersnaam bestaat al.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO [User] (username, password, first_name, last_name, role, is_admin)
                                        VALUES (?, ?, ?, ?, 'Personnel', ?)");
                $stmt->execute([$username, $hashed_password, $first_name, $last_name, $is_admin]);
                $melding = "Personeelslid succesvol toegevoegd.";
            }
        } catch (PDOException $e) {
            $fout = "Fout bij opslaan: " . $e->getMessage();
        }
    }
}
?>

<!-- Form voor het registreren van personeel -->
<h2>Personeelslid registreren</h2>

<?php if ($fout): ?>
    <p style="color:red;"><?= htmlspecialchars($fout) ?></p>
<?php endif; ?>

<?php if ($melding): ?>
    <p style="color:green;"><?= htmlspecialchars($melding) ?></p>
<?php endif; ?>

<form method="post">
    <label>Gebruikersnaam:</label><br>
    <input type="text" name="username" required><br><br>

    <label>Wachtwoord:</label><br>
    <input type="password" name="password" required><br><br>

    <label>Voornaam:</label><br>
    <input type="text" name="first_name" required><br><br>

    <label>Achternaam:</label><br>
    <input type="text" name="last_name" required><br><br>

    <label><input type="checkbox" name="is_admin"> Admin-rechten toekennen</label><br><br>

    <input type="submit" value="Aanmaken">
</form>

<br>
<a href="personeel_beheer.php">Terug naar personeelsoverzicht</a>
