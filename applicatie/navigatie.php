<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
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
    <?php if (isset($_SESSION["username"])): ?>
        <?php if (strtolower($_SESSION["role"]) === "client"): ?>
            <a href="menu.php">🏠 Menu</a>
            <a href="winkelmandje.php">🛒 Winkelmandje</a>
            <a href="profiel.php">👤 Mijn bestellingen</a>
        <?php elseif (strtolower($_SESSION["role"]) === "personnel"): ?>
            <a href="personeel_bestellingen.php">📦 Bestellingen beheren</a>
        <?php endif; ?>
        <a href="loguit.php">🚪 Uitloggen (<?= htmlspecialchars($_SESSION["username"]) ?>)</a>
    <?php else: ?>
        <a href="login.php">🔑 Inloggen</a>
        <a href="registratie.php">📝 Registreren</a>
    <?php endif; ?>
</div>
