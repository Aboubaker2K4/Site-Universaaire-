
<?php
require_once __DIR__ . '/../src/Teacher.php';
require_once __DIR__ . '/../src/Subject.php';
require_once __DIR__ . '/../src/FileManager.php';
require_once __DIR__ . '/../src/Database.php';

$matricule = $_GET['m'] ?? '';
if (!$matricule) { header('Location: teacher_form.php'); exit; }

$teacher = new Teacher();
$info = $teacher->getInfo($matricule);
if (!$info) die("Professeur introuvable.");

$matiere_id = $info['matiere_id'];

// Récupérer le nom de la matière
$db = new Database();
$conn = $db->getConnection();
$stmt = $conn->prepare("SELECT nom FROM matieres WHERE id = ?");
$stmt->execute([$matiere_id]);
$matiereNom = $stmt->fetchColumn() ?: 'Matière';

$fm = new FileManager();
$msg = '';

// TRAITEMENT
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_course']) && !empty($_FILES['fichier'])) {
        if($fm->addCourse($matiere_id, $_FILES['fichier'])) $msg = "Cours ajouté !";
    }
    elseif (isset($_POST['add_tp']) && !empty($_FILES['fichier'])) {
        if($fm->addTP($matiere_id, $_FILES['fichier'])) $msg = "TP ajouté !";
    }
    elseif (isset($_POST['add_video'])) {
        if($fm->addVideo($matiere_id, $_POST['titre_video'], $_POST['url_video'])) $msg = "Vidéo ajoutée !";
    }
    elseif (isset($_POST['delete_item'])) {
        $type = $_POST['del_type'];
        $id_to_del = $_POST['del_id'];
        if($fm->deleteResource($type, $id_to_del)) $msg = "Ressource supprimée.";
    }
}

// RECUPERATION DONNEES
$sub = new Subject();
$coursList = $sub->getCourses($matiere_id);
$tpList = $sub->getTPs($matiere_id);
$videoList = $sub->getVideos($matiere_id);
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Espace Enseignant - EMSI</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    /* RESET */
    * { box-sizing: border-box; margin: 0; padding: 0; }
    
    body { 
        font-family: 'Inter', sans-serif; 
        color: #1f2937;
        /* FOND EMSI */
        background: linear-gradient(rgba(0, 80, 40, 0.85), rgba(0, 60, 30, 0.8)), 
                    url('uploads/images/backsubject.png'); 
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    /* NAVBAR VERT EMSI */
    .navbar { 
        background-color: #007A33; 
        height: 80px; 
        display: flex; 
        align-items: center; 
        justify-content: space-between; 
        padding: 0 40px; 
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        position: relative; z-index: 50;
    }

    /* LOGO */
    .brand { display: flex; align-items: center; gap: 15px; text-decoration: none; }
    .logo-img { height: 55px; background: white; padding: 5px; border-radius: 6px; }
    .brand-text { display: flex; flex-direction: column; color: white; }
    .brand-title { font-weight: 800; font-size: 22px; letter-spacing: 1px; }
    .brand-subtitle { font-size: 11px; font-weight: 500; opacity: 0.9; text-transform: uppercase; }

    /* MENU BURGER */
    .user-area { display: flex; align-items: center; gap: 20px; }
    .user-info { color: white; font-size: 15px; font-weight: 600; }
    
    .burger-btn {
        background: none; border: none; cursor: pointer; padding: 8px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 6px; transition: background 0.2s;
        border: 1px solid rgba(255,255,255,0.3);
    }
    .burger-btn:hover { background-color: rgba(255,255,255,0.2); }
    
    .dropdown-menu {
        display: none; position: absolute; top: 70px; right: 40px;
        background: white; width: 200px; border-radius: 8px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        border: 1px solid #e5e7eb; overflow: hidden; z-index: 100;
    }
    .dropdown-item {
        display: flex; align-items: center; gap: 10px;
        padding: 15px 20px; text-decoration: none; color: #ef4444; 
        font-weight: 600; font-size: 14px; transition: background 0.1s;
    }
    .dropdown-item:hover { background-color: #fee2e2; }

    /* CONTAINER */
    .container { max-width: 1400px; margin: 40px auto; padding: 0 20px; width: 100%; }

    /* HEADER PAGE */
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
    .page-title h1 { font-size: 2.2rem; font-weight: 800; margin: 0; color: white; text-shadow: 0 2px 5px rgba(0,0,0,0.3); }
    .page-title span { color: #d1fae5; font-weight: 600; font-size: 1.1rem; margin-top: 5px; display: block; }
    
    .btn-scroll { 
        background: white; color: #007A33; 
        padding: 12px 24px; border-radius: 30px; 
        text-decoration: none; font-weight: 700; 
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        transition: transform 0.2s; 
    }
    .btn-scroll:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(0,0,0,0.3); }

    /* GRILLE 3 COLONNES */
    .grid-3 { display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 30px; }

    /* PANNEAUX LISTE */
    .panel { background: white; border-radius: 12px; box-shadow: 0 10px 20px rgba(0,0,0,0.2); overflow: hidden; border: none; }
    .panel-cours { border-top: 6px solid #3b82f6; }
    .panel-tp { border-top: 6px solid #10b981; }
    .panel-video { border-top: 6px solid #ef4444; }
    
    .panel-header { padding: 15px 20px; font-weight: 700; border-bottom: 1px solid #f3f4f6; color: #374151; background: #fafafa; }
    .panel-body { padding: 0; max-height: 300px; overflow-y: auto; }
    
    .list-item { display: flex; justify-content: space-between; align-items: center; padding: 12px 20px; border-bottom: 1px solid #f9fafb; font-size: 14px; }
    .list-item:hover { background-color: #f9fafb; }
    
    .btn-delete { background: none; border: none; cursor: pointer; color: #9ca3af; padding: 5px; transition: color 0.2s; }
    .btn-delete:hover { color: #ef4444; }

    /* SECTION UPLOAD */
    .upload-section { margin-top: 60px; border-top: 1px solid rgba(255,255,255,0.2); padding-top: 40px; }
    .section-title { font-size: 1.8rem; font-weight: 700; margin-bottom: 25px; color: white; text-shadow: 0 2px 4px rgba(0,0,0,0.3); }

    /* CARTES FORMULAIRES */
    .form-card { 
        background: white; padding: 25px; 
        border-radius: 12px; 
        box-shadow: 0 10px 20px rgba(0,0,0,0.2); 
    }
    .form-card h3 { margin-top: 0; font-size: 16px; font-weight: 700; margin-bottom: 20px; display: flex; align-items: center; gap: 8px; }
    
    .icon-c { color: #3b82f6; } .icon-t { color: #10b981; } .icon-v { color: #ef4444; }

    .form-group { margin-bottom: 15px; }
    .form-group input { 
        width: 100%; padding: 12px; 
        border: 2px solid #e5e7eb; border-radius: 8px; 
        font-size: 14px; outline: none; transition: border-color 0.2s;
    }
    .form-group input:focus { border-color: #007A33; }
    
    .btn-submit { width: 100%; padding: 12px; border: none; border-radius: 8px; color: white; font-weight: 700; cursor: pointer; transition: transform 0.2s; }
    .btn-submit:hover { transform: translateY(-2px); }
    
    /* Boutons de couleurs spécifiques pour les types */
    .btn-blue { background: #3b82f6; } 
    .btn-green { background: #10b981; } 
    .btn-orange { background: #f59e0b; }

    .alert { padding: 15px; background: #dcfce7; color: #166534; border-radius: 8px; margin-bottom: 20px; border: 1px solid #bbf7d0; text-align: center; font-weight: 600; }
  </style>
</head>
<body>

  <nav class="navbar">
    <div class="brand">
        <img src="uploads/images/logo.png" alt="Logo EMSI" class="logo-img">
        <div class="brand-text">
            <span class="brand-title">EMSI</span>
            <span class="brand-subtitle">PORTAIL ACADÉMIQUE</span>
        </div>
    </div>
    
    <div class="user-area">
        <span class="user-info"><?= htmlspecialchars($info['nom'] . ' ' . $info['prenom']) ?></span>
        
        <button class="burger-btn" onclick="toggleMenu()" title="Menu">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="3" y1="12" x2="21" y2="12"></line>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
        </button>

        <div id="dropdownMenu" class="dropdown-menu">
            <a href="index.php" class="dropdown-item">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                Déconnexion
            </a>
        </div>
    </div>
  </nav>

  <div class="container">
    <?php if($msg): ?><div class="alert"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

    <div class="page-header">
        <div class="page-title">
            <h1>Espace Professeur</h1>
            <span>Matière : <?= htmlspecialchars($matiereNom) ?></span>
        </div>
        <a href="#upload-zone" class="btn-scroll">↓ Ajouter un Chapitre</a>
    </div>

    <div class="grid-3">
        <div class="panel panel-cours">
            <div class="panel-header">Cours</div>
            <div class="panel-body">
                <?php foreach($coursList as $c): ?>
                <div class="list-item">
                    <span>📄 <?= htmlspecialchars($c['filename']) ?></span>
                    <form method="post" onsubmit="return confirm('Supprimer ce cours ?');" style="margin:0;">
                        <input type="hidden" name="del_type" value="cours">
                        <input type="hidden" name="del_id" value="<?= $c['id'] ?>">
                        <button type="submit" name="delete_item" class="btn-delete" title="Supprimer">🗑️</button>
                    </form>
                </div>
                <?php endforeach; ?>
                <?php if(empty($coursList)) echo '<div style="padding:15px; color:#9ca3af; font-size:13px;">Aucun cours.</div>'; ?>
            </div>
        </div>

        <div class="panel panel-tp">
            <div class="panel-header">TP & TD</div>
            <div class="panel-body">
                <?php foreach($tpList as $t): ?>
                <div class="list-item">
                    <span>🧪 <?= htmlspecialchars($t['filename']) ?></span>
                    <form method="post" onsubmit="return confirm('Supprimer ce TP ?');" style="margin:0;">
                        <input type="hidden" name="del_type" value="tp">
                        <input type="hidden" name="del_id" value="<?= $t['id'] ?>">
                        <button type="submit" name="delete_item" class="btn-delete" title="Supprimer">🗑️</button>
                    </form>
                </div>
                <?php endforeach; ?>
                <?php if(empty($tpList)) echo '<div style="padding:15px; color:#9ca3af; font-size:13px;">Aucun TP.</div>'; ?>
            </div>
        </div>

        <div class="panel panel-video">
            <div class="panel-header">Vidéos</div>
            <div class="panel-body">
                <?php foreach($videoList as $v): ?>
                <div class="list-item">
                    <span>🎬 <?= htmlspecialchars($v['title']) ?></span>
                    <form method="post" onsubmit="return confirm('Supprimer cette vidéo ?');" style="margin:0;">
                        <input type="hidden" name="del_type" value="video">
                        <input type="hidden" name="del_id" value="<?= $v['id'] ?>">
                        <button type="submit" name="delete_item" class="btn-delete" title="Supprimer">🗑️</button>
                    </form>
                </div>
                <?php endforeach; ?>
                <?php if(empty($videoList)) echo '<div style="padding:15px; color:#9ca3af; font-size:13px;">Aucune vidéo.</div>'; ?>
            </div>
        </div>
    </div>

    <div id="upload-zone" class="upload-section">
        <h2 class="section-title">Ajouter du contenu</h2>
        <div class="grid-3">
            <div class="form-card">
                <h3 class="icon-c">📄 Ajouter un Cours (PDF)</h3>
                <form method="post" enctype="multipart/form-data">
                    <div class="form-group"><input type="file" name="fichier" required accept=".pdf"></div>
                    <button type="submit" name="add_course" class="btn-submit btn-blue">Publier le Cours</button>
                </form>
            </div>
            <div class="form-card">
                <h3 class="icon-t">🧪 Ajouter un TP (PDF)</h3>
                <form method="post" enctype="multipart/form-data">
                    <div class="form-group"><input type="file" name="fichier" required accept=".pdf"></div>
                    <button type="submit" name="add_tp" class="btn-submit btn-green">Publier le TP</button>
                </form>
            </div>
            <div class="form-card">
                <h3 class="icon-v">🎬 Ajouter une Vidéo</h3>
                <form method="post">
                    <div class="form-group"><input type="text" name="titre_video" placeholder="Titre" required></div>
                    <div class="form-group"><input type="text" name="url_video" placeholder="Lien URL" required></div>
                    <button type="submit" name="add_video" class="btn-submit btn-orange">Ajouter la vidéo</button>
                </form>
            </div>
        </div>
    </div>
  </div>

  <script>
    function toggleMenu() {
        const menu = document.getElementById('dropdownMenu');
        menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
    }
    
    window.onclick = function(event) {
        if (!event.target.closest('.burger-btn')) {
            document.getElementById('dropdownMenu').style.display = 'none';
        }
    }
  </script>

</body>
</html>