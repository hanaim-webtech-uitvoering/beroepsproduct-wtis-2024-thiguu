<?php
require_once("db_connectie.php");

$error = "";
// Aanmaken variabele form
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username   = trim($_POST["username"]);
    $password   = trim($_POST["password"]);
    $first_name = trim($_POST["first_name"]);
    $last_name  = trim($_POST["last_name"]);
    $role       = "Client"; // Alleen klanten registreren
    $address    = trim($_POST["address"]);

    try {
        $conn = maakVerbinding();

        // Check of gebruiker al bestaat
        $check = $conn->prepare("SELECT username FROM [User] WHERE username = ?");
        $check->execute([$username]);
        if ($check->fetch()) {
            $error = "Gebruikersnaam bestaat al.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO [User] (username, password, first_name, last_name, address, role) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$username, $hashed_password, $first_name, $last_name, $address, $role]);

            header("Location: login.php");
            exit();
        }
    } catch (PDOException $e) {
        $error = "Fout bij registreren: " . $e->getMessage();
    }
}
?>
<!-- css inladen -->
<link rel="stylesheet" href="style.css">
<!-- Tonen form voor het registreren -->
<h2>Registreren</h2>
<form method="post">
    <label>Gebruikersnaam:</label><br>
    <input type="text" name="username" required><br>

    <label>Wachtwoord:</label><br>
    <input type="password" name="password" required><br>

    <label>Voornaam:</label><br>
    <input type="text" name="first_name" required><br>

    <label>Achternaam:</label><br>
    <input type="text" name="last_name" required><br>

    <label>Adres (voor bezorging):</label><br>
    <input type="text" name="address"><br>

    <input type="submit" value="Registreren">
</form>
<p style="color:red"><?= htmlspecialchars($error) ?></p>
