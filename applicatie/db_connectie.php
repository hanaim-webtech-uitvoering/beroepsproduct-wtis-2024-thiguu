<?php
$db_host     = 'database_server';        // Docker service name
$db_name     = 'pizzeria';               // Database naam
$db_user     = 'sa';
$db_password = 'abc123!@#';              // Hardcoded wachtwoord

try {
    $verbinding = new PDO(
        "sqlsrv:Server=$db_host;Database=$db_name;ConnectionPooling=0;TrustServerCertificate=1",
        $db_user,
        $db_password
    );

    // Zet foutmodus op uitzondering
    $verbinding->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Databaseverbinding mislukt: " . $e->getMessage());
}

// Functie om de verbinding op te halen in andere bestanden
function maakVerbinding() {
    global $verbinding;
    return $verbinding;
}
?>
