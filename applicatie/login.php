<?php
session_start();
require_once(__DIR__ . "/includes/db_connectie.php");
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    try {
        $conn = maakVerbinding();

        $stmt = $conn->prepare("SELECT username, password, role FROM [User] WHERE username = ?");
        $stmt->execute([$username]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION["username"] = $user["username"];
            $_SESSION["role"]     = $user["role"];

            if (strtolower($user["role"]) === "personnel") {
                header("Location: personeel_bestellingen.php");
            } else {
                header("Location: menu.php");
            }
            exit();
        } else {
            $error = "Onjuiste gebruikersnaam of wachtwoord.";
        }
    } catch (PDOException $e) {
        $error = "Fout bij inloggen: " . $e->getMessage();
    }
}
?>
<?php
include(__DIR__ . "/includes/header.php");
include(__DIR__ . "/includes/navigatie.php");
?>
<!-- HTML FORM -->
<h2>Inloggen</h2>
<form method="post">
    <label>Gebruikersnaam:</label><br>
    <input type="text" name="username" required><br>

    <label>Wachtwoord:</label><br>
    <input type="password" name="password" required><br>

    <input type="submit" value="Inloggen">
</form>
<p style="color:red"><?= htmlspecialchars($error) ?></p>
