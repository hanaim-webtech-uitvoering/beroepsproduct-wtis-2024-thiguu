<?php
require_once("db_connectie.php");
$conn = maakVerbinding();

try {
    // Haal alleen gebruikers op met nog geen gehashte wachtwoorden
    $stmt = $conn->query("SELECT username, password FROM [User]");
    $gebruikers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($gebruikers as $gebruiker) {
        $username = $gebruiker["username"];
        $password = $gebruiker["password"];

        // Als wachtwoord al een bcrypt-hash is, sla over
        if (str_starts_with($password, '$2y$')) {
            continue;
        }

        // Anders: hash en opslaan
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE [User] SET password = ? WHERE username = ?");
        $update->execute([$hash, $username]);

        echo "✅ Gehashed voor: $username\n";
    }

    echo "✅ Klaar!";
} catch (PDOException $e) {
    echo "Fout: " . $e->getMessage();
}
