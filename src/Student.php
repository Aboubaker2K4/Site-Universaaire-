<!-- ?php
require_once __DIR__ . '/Database.php';

class Student {
    private $db;
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function exists(string $matricule): bool {
        $stmt = $this->db->prepare('SELECT matricule FROM etudiants WHERE matricule = ?');
        $stmt->execute([$matricule]);
        return (bool)$stmt->fetchColumn();
    }

    public function getByMatricule(string $matricule) {
        $stmt = $this->db->prepare('SELECT * FROM etudiants WHERE matricule = ?');
        $stmt->execute([$matricule]);
        return $stmt->fetch();
    }

    public function create(array $data) {
        $stmt = $this->db->prepare('INSERT INTO etudiants (matricule, nom, prenom, filiere, annee) VALUES (?, ?, ?, ?, ?)');
        return $stmt->execute([$data['matricule'], $data['nom'], $data['prenom'], $data['filiere'], $data['annee']]);
    }
}
--> 
<?php
require_once 'Database.php';

class Student {
    private $conn;
    private $table = 'etudiants';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Fonction de connexion sécurisée (Nom + Prénom + Matricule)
    public function checkLogin($matricule, $nomInput, $prenomInput) {
        $info = $this->getInfo($matricule);
        if ($info) {
            $dbNom = trim($info['nom']);
            $dbPrenom = trim($info['prenom']);
            $inputNom = trim($nomInput);
            $inputPrenom = trim($prenomInput);

            if (strcasecmp($dbNom, $inputNom) === 0 && strcasecmp($dbPrenom, $inputPrenom) === 0) {
                return true;
            }
        }
        return false;
    }

    // Récupérer les infos complètes
    public function getInfo($matricule) {
        $query = "SELECT * FROM " . $this->table . " WHERE matricule = :matricule LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':matricule', $matricule);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // CORRECTION : On rajoute cette méthode qui manquait
    public function exists($matricule) {
        return $this->getInfo($matricule) !== false;
    }
}
?>