<!--?php
require_once __DIR__ . '/Database.php';

class Admin {
    private $db;
    public function __construct(){
        $this->db = Database::getInstance()->getConnection();
    }

    public function checkCredentials(string $id_admin, string $password): bool {
        $stmt = $this->db->prepare('SELECT password_hash FROM admins WHERE id_admin = ?');
        $stmt->execute([$id_admin]);
        $row = $stmt->fetch();
        if (!$row) return false;
        return password_verify($password, $row['password_hash']);
    }

    public function exists(string $id_admin): bool {
        $stmt = $this->db->prepare('SELECT id FROM admins WHERE id_admin = ?');
        $stmt->execute([$id_admin]);
        return (bool)$stmt->fetchColumn();
    }
}
--> 
<?php
// Fichier : src/Admin.php
require_once 'Database.php';

class Admin {
    private $conn;
    private $table = 'admins';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // NOUVELLE VÉRIFICATION : ID + Password + Nom + Prénom
    public function checkCredentials($id_admin, $passwordInput, $nomInput, $prenomInput) {
        // 1. On récupère l'admin via son ID unique
        $query = "SELECT * FROM " . $this->table . " WHERE id_admin = :id_admin LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_admin', $id_admin);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // 2. Vérification du Mot de passe (Texte clair comme demandé précédemment)
            if ($passwordInput !== $row['password']) {
                return false;
            }

            // 3. Vérification du Nom et Prénom (Insensible à la casse)
            $dbNom = trim($row['nom']);
            $dbPrenom = trim($row['prenom']);
            $inNom = trim($nomInput);
            $inPrenom = trim($prenomInput);

            if (strcasecmp($dbNom, $inNom) === 0 && strcasecmp($dbPrenom, $inPrenom) === 0) {
                return true; // TOUT correspond
            }
        }
        return false; // ID faux, ou MDP faux, ou Nom/Prénom faux
    }

    public function exists($id_admin) {
        $query = "SELECT id_admin FROM " . $this->table . " WHERE id_admin = :id_admin";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_admin', $id_admin);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
?>