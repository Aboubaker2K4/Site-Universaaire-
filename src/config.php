<!--?php
// Configuration de la base de données - ajustez selon votre environnement
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'university');
define('DB_USER', 'root');
define('DB_PASS', '');

define('UPLOAD_DIR', __DIR__ . '/../public/uploads');
?>
--> 
<?php
// Fichier : src/config.php
// Si vous utilisez Database.php, ce fichier sert juste à définir des constantes globales si besoin.

define('DB_HOST', 'localhost');
define('DB_NAME', 'university');
define('DB_USER', 'root');
define('DB_PASS', '');

// Chemin racine du projet (utile pour les inclusions)
define('ROOT_PATH', dirname(__DIR__));
?> 