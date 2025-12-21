<!--?php
require_once __DIR__ . '/../src/Admin.php';
require_once __DIR__ . '/../src/Student.php';
require_once __DIR__ . '/../src/Teacher.php';
$id = $_GET['id'] ?? '';
$a = new Admin();
if ($id && !$a->exists($id)) { header('Location: admin_form.php'); exit; }
$error='';
if ($_SERVER['REQUEST_METHOD']==='POST'){
  $type = $_POST['type'] ?? '';
  $key = $_POST['key'] ?? '';
  if($type==='etudiant'){
    $s = new Student();
    if(!$s->exists($key)) $error='Étudiant introuvable'; else header('Location: info.php?type=etudiant&key=' . urlencode($key));
  } else {
    $t = new Teacher();
    if(!$t->exists($key)) $error='Enseignant introuvable'; else header('Location: info.php?type=enseignant&key=' . urlencode($key));
  }
  exit;
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="assets/style.css"><title>Gérer</title></head>
<body>
<div class="container">
  <div class="card header">
    <h2>Espace Admin</h2>
    <a class="btn" href="index.php">Retour Accueil</a>
  </div>
  <div class="card" style="margin-top:12px">
    ?php if($error): ?><div class="error">?=htmlspecialchars($error)?></div>?php endif; ?>
    <form method="post">
      <label>Type</label>
      <select name="type"><option value="etudiant">Étudiant</option><option value="enseignant">Enseignant</option></select>
      <input name="key" placeholder="Matricule / ID">
      <button class="btn" type="submit">Rechercher</button>
      <a class="btn secondary" href="add.php">Ajouter</a>
    </form>
  </div>
</div>
</body></html>
    --> 
<?php
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Student.php';
require_once __DIR__ . '/../src/Teacher.php';

$id_admin = $_GET['id'] ?? '';
if (!$id_admin) { header('Location: admin_form.php'); exit; }

$db = new Database();
$conn = $db->getConnection();

// --- CHARGEMENT DES LISTES (Pour les menus ET pour l'affichage) ---
$listeFilieres = [];
$stmtF = $conn->query("SELECT * FROM filieres ORDER BY nom ASC");
while($row = $stmtF->fetch(PDO::FETCH_ASSOC)) {
    $listeFilieres[$row['id']] = $row['nom'];
}

$listeMatieres = [];
$stmtM = $conn->query("SELECT * FROM matieres ORDER BY nom ASC");
while($row = $stmtM->fetch(PDO::FETCH_ASSOC)) {
    $listeMatieres[$row['id']] = $row['nom'];
}
// ---------------------------------------------------------------------

$searchResult = null;
$msg = "";

// 1. TRAITEMENT RECHERCHE
if (isset($_POST['search'])) {
    $type = $_POST['user_type'];
    $matricule = trim($_POST['search_matricule']);

    if ($type === 'etudiant') {
        $s = new Student();
        $searchResult = $s->getInfo($matricule);
        if ($searchResult) {
            $searchResult['type_label'] = 'etudiant';
            $fid = $searchResult['filiere_id'];
            $searchResult['display_filiere'] = $listeFilieres[$fid] ?? 'Inconnue';
        }
    } else {
        $t = new Teacher();
        $searchResult = $t->getInfo($matricule);
        if ($searchResult) {
            $searchResult['type_label'] = 'enseignant';
            $mid = $searchResult['matiere_id'];
            $searchResult['display_matiere'] = $listeMatieres[$mid] ?? 'Inconnue';
        }
    }
    
    if (!$searchResult) {
        $msg = "Aucun utilisateur trouvé avec ce matricule.";
    }
}

// 2. TRAITEMENT SUPPRESSION
if (isset($_POST['delete_user'])) {
    $delMatricule = $_POST['del_matricule'];
    $delType = $_POST['del_type']; 

    try {
        if ($delType === 'etudiant') {
            $sql = "DELETE FROM etudiants WHERE matricule = ?";
        } else {
            $sql = "DELETE FROM enseignants WHERE matricule = ?";
        }
        $stmt = $conn->prepare($sql);
        $stmt->execute([$delMatricule]);
        
        $msg = "Utilisateur ($delMatricule) supprimé avec succès !";
        $searchResult = null; 
        
    } catch (PDOException $e) {
        $msg = "Erreur lors de la suppression : " . $e->getMessage();
    }
}

// 3. TRAITEMENT AJOUT
if (isset($_POST['add_user'])) {
    $newMat = $_POST['new_matricule'];
    $newNom = $_POST['new_nom'];
    $newPre = $_POST['new_prenom'];
    $userType = $_POST['new_type']; // 1=Etu, 2=Prof
    
    try {
        if ($userType == '1') {
            $fil = $_POST['new_filiere'];
            $sql = "INSERT INTO etudiants (matricule, nom, prenom, annee, filiere_id) VALUES (?, ?, ?, '2024-2025', ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$newMat, $newNom, $newPre, $fil]);
        } else {
            $mat = $_POST['new_matiere'];
            $sql = "INSERT INTO enseignants (matricule, nom, prenom, matiere_id) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$newMat, $newNom, $newPre, $mat]);
        }
        $msg = "Utilisateur ajouté avec succès !";
    } catch (PDOException $e) {
        $msg = "Erreur : " . $e->getMessage();
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Administration - EMSI</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    /* RESET */
    * { box-sizing: border-box; margin: 0; padding: 0; }
    
    body { 
        font-family: 'Inter', sans-serif; 
        color: #1f2937;
        /* FOND EMSI */
        background: linear-gradient(rgba(0, 80, 40, 0.85), rgba(0, 60, 30, 0.8)), 
                    url('uploads/images/backindex.jpg'); 
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
        padding: 15px 20px; text-decoration: none; font-weight: 600; font-size: 14px; 
        color: #374151; transition: background 0.1s;
    }
    .dropdown-item:hover { background-color: #f3f4f6; }
    .item-red { color: #ef4444; border-top: 1px solid #f3f4f6; }
    .item-red:hover { background-color: #fee2e2; }

    /* CONTENU PRINCIPAL */
    .container { max-width: 1000px; margin: 40px auto; padding: 0 20px; width: 100%; }

    /* HEADER SECTION */
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
    .page-title { font-size: 2.2rem; font-weight: 800; color: white; text-shadow: 0 2px 5px rgba(0,0,0,0.3); margin: 0; }
    
    /* BOUTON VERT (Ajouter) */
    .btn-add { 
        background-color: #007A33; color: white; border: 2px solid rgba(255,255,255,0.5);
        padding: 12px 24px; border-radius: 30px; text-decoration: none; font-weight: 700; 
        display: inline-flex; align-items: center; gap: 8px; cursor: pointer; font-size: 14px; 
        box-shadow: 0 4px 10px rgba(0,0,0,0.2); transition: transform 0.2s, background 0.2s; 
    }
    .btn-add:hover { background-color: #005c26; transform: translateY(-2px); border-color: white; }

    /* CARTE DE RECHERCHE */
    .search-card { 
        background: white; border-radius: 12px; padding: 0; 
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2); border: none; overflow: hidden; margin-bottom: 30px; 
    }
    .card-header { padding: 20px 24px; border-bottom: 1px solid #f3f4f6; font-weight: 700; color: #374151; font-size: 16px; background: #fafafa; }
    .card-body { padding: 30px 24px; }

    /* FORMULAIRE RECHERCHE */
    .radio-group { display: flex; gap: 20px; margin-bottom: 20px; }
    .radio-label { display: flex; align-items: center; gap: 8px; font-size: 14px; color: #4b5563; cursor: pointer; font-weight: 500; }
    .search-row { display: flex; gap: 10px; }
    
    /* INPUT STYLE */
    .input-std { 
        flex: 1; background-color: #f9fafb; border: 2px solid #e5e7eb; color: #1f2937; 
        padding: 12px 16px; border-radius: 8px; outline: none; font-size: 14px; transition: border-color 0.2s;
    }
    .input-std:focus { border-color: #007A33; background: white; }

    /* BOUTON BLEU (Rechercher) */
    .btn-search { 
        background-color: #2563eb; color: white; padding: 0 24px; border-radius: 8px; border: none; 
        font-weight: 600; cursor: pointer; font-size: 14px; display: flex; align-items: center; gap: 8px; 
        transition: background 0.2s;
    }
    .btn-search:hover { background-color: #1d4ed8; }
    
    /* BOUTON ROUGE (Supprimer) */
    .btn-delete { 
        background-color: #ef4444; color: white; padding: 10px 20px; border-radius: 6px; border: none; 
        font-weight: 600; cursor: pointer; font-size: 14px; margin-top: 20px; transition: background 0.2s; 
    }
    .btn-delete:hover { background-color: #dc2626; }

    /* RÉSULTATS */
    .result-box { 
        background: white; border-radius: 12px; border: none; 
        padding: 25px; margin-top: 20px; box-shadow: 0 10px 20px rgba(0,0,0,0.2); 
    }
    .result-row { display: flex; justify-content: space-between; border-bottom: 1px solid #f3f4f6; padding: 12px 0; }
    .result-row:last-child { border-bottom: none; }
    .label { font-weight: 600; color: #6b7280; }
    
    /* MODAL D'AJOUT */
    #addModal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(5px); }
    .modal-content { background: white; width: 500px; padding: 30px; border-radius: 16px; box-shadow: 0 20px 40px rgba(0,0,0,0.3); }
    .modal-title { font-size: 20px; font-weight: 800; margin-bottom: 20px; color: #1f2937; }
    
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; margin-bottom: 5px; font-size: 14px; font-weight: 600; color: #374151; }
    .form-control { width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 8px; outline: none; transition: border-color 0.2s; }
    .form-control:focus { border-color: #007A33; }
    
    .modal-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px; }
    .btn-cancel { background: #e5e7eb; color: #374151; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; }
    
    /* Bouton enregistrer vert */
    .btn-save { background: #007A33; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; }
    .btn-save:hover { background: #005c26; }

    .alert { padding: 15px; background: #dcfce7; color: #166534; border-radius: 8px; margin-bottom: 20px; border: 1px solid #bbf7d0; text-align: center; font-weight: 600; }
    .alert-error { background: #fee2e2; color: #991b1b; border-color: #fecaca; }
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
        <span class="user-info">Administrateur</span>
        
        <button class="burger-btn" onclick="toggleMenu()" title="Menu">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
        </button>

        <div id="dropdownMenu" class="dropdown-menu">
            <a href="index.php" class="dropdown-item item-red">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                Déconnexion
            </a>
        </div>
    </div>
  </nav>

  <div class="container">
    
    <?php if($msg): ?>
        <div class="alert <?= strpos($msg, 'Erreur') !== false ? 'alert-error' : '' ?>">
            <?= htmlspecialchars($msg) ?>
        </div>
    <?php endif; ?>

    <div class="page-header">
      <h1 class="page-title">Gérer l'université</h1>
      <button class="btn-add" onclick="openModal()">
        <span>+</span> Ajouter Utilisateur
      </button>
    </div>

    <div class="search-card">
      <div class="card-header">Rechercher un utilisateur</div>
      <div class="card-body">
        <form method="post">
          <div class="radio-group">
            <label class="radio-label">
              <input type="radio" name="user_type" value="etudiant" checked> Étudiant
            </label>
            <label class="radio-label">
              <input type="radio" name="user_type" value="enseignant"> Enseignant
            </label>
          </div>
          <div class="form-group" style="margin-bottom: 5px;">
             <label style="font-size:13px; font-weight:600; margin-bottom:5px; display:block;">ID (Matricule)</label>
          </div>
          <div class="search-row">
            <input type="text" name="search_matricule" class="input-std" placeholder="Saisir un matricule..." required>
            <button type="submit" name="search" class="btn-search">Rechercher</button>
          </div>
        </form>
      </div>
    </div>

    <?php if ($searchResult): ?>
    <div class="result-box">
        <h3 style="margin-bottom: 15px; color:#1f2937; margin-top:0;">Résultat trouvé</h3>
        <div class="result-row"><span class="label">Type</span> <span><?= ($searchResult['type_label'] == 'etudiant') ? 'Étudiant' : 'Enseignant' ?></span></div>
        <div class="result-row"><span class="label">Matricule</span> <span><?= htmlspecialchars($searchResult['matricule']) ?></span></div>
        <div class="result-row"><span class="label">Nom</span> <span><?= htmlspecialchars($searchResult['nom']) ?></span></div>
        <div class="result-row"><span class="label">Prénom</span> <span><?= htmlspecialchars($searchResult['prenom']) ?></span></div>
        
        <?php if($searchResult['type_label'] == 'etudiant'): ?>
            <div class="result-row">
                <span class="label">Filière</span> 
                <span><?= htmlspecialchars($searchResult['display_filiere']) ?></span>
            </div>
        <?php elseif($searchResult['type_label'] == 'enseignant'): ?>
            <div class="result-row">
                <span class="label">Matière enseignée</span> 
                <span><?= htmlspecialchars($searchResult['display_matiere']) ?></span>
            </div>
        <?php endif; ?>

        <form method="post" onsubmit="return confirm('⚠️ Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">
            <input type="hidden" name="del_matricule" value="<?= htmlspecialchars($searchResult['matricule']) ?>">
            <input type="hidden" name="del_type" value="<?= htmlspecialchars($searchResult['type_label']) ?>">
            <button type="submit" name="delete_user" class="btn-delete">🗑️ Supprimer l'utilisateur</button>
        </form>
    </div>
    <?php endif; ?>

  </div>

  <div id="addModal">
    <div class="modal-content">
      <div class="modal-title">Ajouter un nouvel utilisateur</div>
      <form method="post">
        <div class="form-group">
            <label>Type de compte</label>
            <select name="new_type" class="form-control" onchange="toggleFields(this.value)">
                <option value="1">Étudiant</option>
                <option value="2">Enseignant</option>
            </select>
        </div>
        
        <div class="form-group"><label>Matricule</label><input type="text" name="new_matricule" class="form-control" required></div>
        <div class="form-group"><label>Nom</label><input type="text" name="new_nom" class="form-control" required></div>
        <div class="form-group"><label>Prénom</label><input type="text" name="new_prenom" class="form-control" required></div>

        <div class="form-group" id="field-filiere">
            <label>Filière</label>
            <select name="new_filiere" class="form-control">
                <?php foreach($listeFilieres as $id => $nom): ?>
                    <option value="<?= $id ?>"><?= htmlspecialchars($nom) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group" id="field-matiere" style="display:none;">
            <label>Matière enseignée</label>
            <select name="new_matiere" class="form-control">
                <?php foreach($listeMatieres as $id => $nom): ?>
                    <option value="<?= $id ?>"><?= htmlspecialchars($nom) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="modal-actions">
            <button type="button" class="btn-cancel" onclick="closeModal()">Annuler</button>
            <button type="submit" name="add_user" class="btn-save">Enregistrer</button>
        </div>
      </form>
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

    function openModal() { document.getElementById('addModal').style.display = 'flex'; }
    function closeModal() { document.getElementById('addModal').style.display = 'none'; }
    
    function toggleFields(val) {
        document.getElementById('field-filiere').style.display = (val == '1') ? 'block' : 'none';
        document.getElementById('field-matiere').style.display = (val == '2') ? 'block' : 'none';
    }
  </script>
</body>
</html>