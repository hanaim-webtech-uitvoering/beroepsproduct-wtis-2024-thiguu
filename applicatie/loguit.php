<?php
session_start();
session_unset();   // Verwijderd alle sessievariabelen
session_destroy(); // Beëindigd de sessie

header("Location: menu.php"); // Redirect naar Menu
exit();
