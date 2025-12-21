 <?php
// Fichier : src/Subject.php
require_once 'Database.php';

class Subject {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // 1. Récupérer les matières d'une filière (Pour le tableau de bord)
    public function getByFiliere($filiere_id) {
        $query = "SELECT m.id, m.nom 
                  FROM matieres m
                  JOIN filiere_matiere fm ON m.id = fm.matiere_id
                  WHERE fm.filiere_id = :filiere_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':filiere_id', $filiere_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. Récupérer les COURS (Table fichiers_cours)
    public function getCourses($matiere_id) {
        $query = "SELECT * FROM fichiers_cours WHERE matiere_id = :id ORDER BY uploaded_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $matiere_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 3. Récupérer les TP (Table fichiers_tp)
    public function getTPs($matiere_id) {
        $query = "SELECT * FROM fichiers_tp WHERE matiere_id = :id ORDER BY uploaded_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $matiere_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 4. Récupérer les VIDÉOS (Table videos)
    public function getVideos($matiere_id) {
        $query = "SELECT * FROM videos WHERE matiere_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $matiere_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
