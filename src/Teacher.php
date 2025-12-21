
<?php
require_once 'Database.php';

class Teacher {
    private $conn;
    private $table = 'enseignants';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

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

    public function getInfo($matricule) {
        $query = "SELECT * FROM " . $this->table . " WHERE matricule = :matricule LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':matricule', $matricule);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // CORRECTION : On rajoute cette méthode
    public function exists($matricule) {
        return $this->getInfo($matricule) !== false;
    }
}
?>