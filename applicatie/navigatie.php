<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once("db_connectie.php");
?>

<style>
.navbar {
    background-color: #f2f2f2;
    padding: 10px;
    margin-bottom: 20px;
    border-bottom: 1px solid #ccc;
}
.navbar a {
    margin-right: 15px;
    text-decoration: none;
    color: #333;
    font-weight: bold;
}
</style>

<div class="navbar">
    <a href="menu.php">🍕 Menu</a>
    <a href="privacyverklaring.php">🔐 Privacyverklaring</a>

    <?php if (isset($_SESSION["username"])): ?>
        <?php if (strtolower($_SESSION["role"]) === "client"): ?>
            <a href="winkelmandje.php">🛒 Winkelmandje</a>
            <a href="profiel.php">👤 Mijn bestellingen</a>

        <?php elseif (strtolower($_SESSION["role"]) === "personnel"): ?>
            <a href="personeel_bestellingen.php">📦 Bestellingen beheren</a>

            <?php
            // Check of gebruiker admin is
            $conn = maakVerbinding();
            $stmt = $conn->prepare("SELECT is_admin FROM [User] WHERE username = ?");
            $stmt->execute([$_SESSION["username"]]);
            $is_admin = $stmt->fetchColumn();

            if ($is_admin): ?>
                <a href="personeel_beheer.php">⚙️ Personeel beheren</a>
                <a href="registratie_personeel.php">➕ Personeel toevoegen</a>
            <?php endif; ?>
        <?php endif; ?>

        <a href="loguit.php">🚪 Uitloggen (<?= htmlspecialchars($_SESSION["username"]) ?>)</a>
    <?php else: ?>
        <a href="login.php">🔐 Inloggen</a>
        <a href="registratie.php">📝 Registreren</a>
    <?php endif; ?>
</div>
