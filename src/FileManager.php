<!--?php
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/config.php';

class FileManager {
    private $db;
    public function __construct(){
        $this->db = Database::getInstance()->getConnection();
        if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0755, true);
    }

    public function saveCourse(int $matiere_id, array $file) {
        $target = UPLOAD_DIR . '/' . basename($file['name']);
        if (move_uploaded_file($file['tmp_name'], $target)) {
            $stmt = $this->db->prepare('INSERT INTO fichiers_cours (matiere_id, filename, filepath) VALUES (?, ?, ?)');
            return $stmt->execute([$matiere_id, $file['name'], 'uploads/' . basename($file['name'])]);
        }
        return false;
    }

    public function saveTP(int $matiere_id, array $file) {
        $target = UPLOAD_DIR . '/' . basename($file['name']);
        if (move_uploaded_file($file['tmp_name'], $target)) {
            $stmt = $this->db->prepare('INSERT INTO fichiers_tp (matiere_id, filename, filepath) VALUES (?, ?, ?)');
            return $stmt->execute([$matiere_id, $file['name'], 'uploads/' . basename($file['name'])]);
        }
        return false;
    }

    public function deleteCourse(int $id) {
        $stmt = $this->db->prepare('SELECT filepath FROM fichiers_cours WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row) {
            @unlink(__DIR__ . '/../public/' . $row['filepath']);
            $stmt = $this->db->prepare('DELETE FROM fichiers_cours WHERE id = ?');
            return $stmt->execute([$id]);
        }
        return false;
    }

    public function deleteTP(int $id) {
        $stmt = $this->db->prepare('SELECT filepath FROM fichiers_tp WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row) {
            @unlink(__DIR__ . '/../public/' . $row['filepath']);
            $stmt = $this->db->prepare('DELETE FROM fichiers_tp WHERE id = ?');
            return $stmt->execute([$id]);
        }
        return false;
    }
}
--> 
<?php
// Fichier : src/FileManager.php
require_once 'Database.php';

class FileManager {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    private function uploadFileToDisk($file, $folder = 'uploads/') {
        $target_dir = __DIR__ . '/../public/' . $folder; 
        if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }

        $filename = basename($file["name"]);
        $target_file = $target_dir . time() . "_" . $filename;
        $db_path = $folder . time() . "_" . $filename; 

        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            return ['filename' => $filename, 'filepath' => $db_path];
        }
        return false;
    }

    public function addCourse($matiere_id, $file) {
        $upload = $this->uploadFileToDisk($file);
        if ($upload) {
            $query = "INSERT INTO fichiers_cours (matiere_id, filename, filepath) VALUES (:id, :name, :path)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':id' => $matiere_id, ':name' => $upload['filename'], ':path' => $upload['filepath']]);
            return true;
        }
        return false;
    }

    public function addTP($matiere_id, $file) {
        $upload = $this->uploadFileToDisk($file);
        if ($upload) {
            $query = "INSERT INTO fichiers_tp (matiere_id, filename, filepath) VALUES (:id, :name, :path)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':id' => $matiere_id, ':name' => $upload['filename'], ':path' => $upload['filepath']]);
            return true;
        }
        return false;
    }

    public function addVideo($matiere_id, $title, $url) {
        $query = "INSERT INTO videos (matiere_id, title, url) VALUES (:id, :title, :url)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':id' => $matiere_id, ':title' => $title, ':url' => $url]);
    }

    // NOUVELLE FONCTION DE SUPPRESSION
    public function deleteResource($type, $id) {
        // type = 'cours', 'tp', or 'video'
        if ($type == 'cours') $table = 'fichiers_cours';
        elseif ($type == 'tp') $table = 'fichiers_tp';
        elseif ($type == 'video') $table = 'videos';
        else return false;

        // 1. On récupère le chemin pour supprimer le fichier physique (optionnel mais propre)
        // (Ici on fait simple, on supprime juste la ligne en BDD)
        
        $query = "DELETE FROM $table WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':id' => $id]);
    }
}
?>