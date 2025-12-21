<?php
require_once __DIR__ . '/../src/Student.php';
require_once __DIR__ . '/../src/Subject.php';
require_once __DIR__ . '/../src/Database.php';

// Récupération des paramètres
$matricule = $_GET['m'] ?? '';
$matiere_id = $_GET['id'] ?? '';

if (!$matricule || !$matiere_id) { header('Location: student.php'); exit; }

$student = new Student();
$info = $student->getInfo($matricule);
if (!$info) die("Erreur accès.");

$subjectManager = new Subject();
$cours = $subjectManager->getCourses($matiere_id);
$tps = $subjectManager->getTPs($matiere_id);
$videos = $subjectManager->getVideos($matiere_id);

// Récupérer le nom de la matière
$db = new Database();
$conn = $db->getConnection();
$stmt = $conn->prepare("SELECT nom FROM matieres WHERE id = ?");
$stmt->execute([$matiere_id]);
$matiereInfo = $stmt->fetch(PDO::FETCH_ASSOC);
$nomMatiere = $matiereInfo['nom'] ?? 'Matière inconnue';
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title><?= htmlspecialchars($nomMatiere) ?> - EMSI</title>
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
        flex-shrink: 0; /* Important pour que la navbar reste fixe */
    }

    .brand { display: flex; align-items: center; gap: 15px; text-decoration: none; }
    .logo-img { height: 55px; background: white; padding: 5px; border-radius: 6px; }
    .brand-text { display: flex; flex-direction: column; color: white; }
    .brand-title { font-weight: 800; font-size: 22px; letter-spacing: 1px; }
    .brand-subtitle { font-size: 11px; font-weight: 500; opacity: 0.9; text-transform: uppercase; }

    .user-area { display: flex; align-items: center; gap: 20px; }
    
    /* BURGER MENU */
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
    .item-blue { color: #007A33; }
    .item-red { color: #ef4444; border-top: 1px solid #f3f4f6; }
    .item-red:hover { background-color: #fee2e2; }

    /* --- CENTRAGE VERTICAL ET HORIZONTAL (Comme student.php) --- */
    .main-content {
        flex: 1; /* Prend tout l'espace disponible */
        display: flex;
        flex-direction: column;
        justify-content: center; /* Centre Verticalement */
        align-items: center;     /* Centre Horizontalement */
        padding: 40px 20px;
        width: 100%;
    }

    .header-row { 
        display: flex; align-items: center; justify-content: center; gap: 15px; 
        margin-bottom: 50px; 
        flex-wrap: wrap;
    }
    
    h1 { 
        font-size: 2.5rem; font-weight: 800; color: white; margin: 0; 
        text-shadow: 0 4px 10px rgba(0,0,0,0.4);
    }
    
    .badge { 
        background: rgba(255,255,255,0.2); 
        color: white; padding: 6px 12px; border-radius: 20px; 
        font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;
        backdrop-filter: blur(5px);
    }

    /* CONTENEUR DES COLONNES */
    .columns-wrapper { 
        display: flex; 
        flex-wrap: wrap;
        justify-content: center; /* Centre les éléments horizontalement */
        gap: 30px; 
        width: 100%;
        max-width: 1400px;
    }

    /* PANNEAUX BLANCS */
    .panel { 
        background: white; 
        width: 350px; /* Largeur fixe pour l'uniformité */
        border-radius: 16px; 
        box-shadow: 0 20px 40px rgba(0,0,0,0.3); 
        overflow: hidden; 
        border: none; 
        display: flex;
        flex-direction: column;
        transition: transform 0.3s;
    }
    .panel:hover { transform: translateY(-5px); }
    
    /* Bordures colorées */
    .panel-cours { border-top: 6px solid #3b82f6; }
    .panel-tp { border-top: 6px solid #10b981; }
    .panel-video { border-top: 6px solid #ef4444; }

    .panel-header { 
        padding: 20px 25px; 
        font-weight: 700; font-size: 18px; 
        border-bottom: 1px solid #f3f4f6; color: #1f2937; 
        background: #fafafa;
    }
    .panel-body { padding: 0; flex: 1; }

    /* ITEMS LISTE */
    .resource-item { 
        display: flex; justify-content: space-between; align-items: center; 
        padding: 16px 25px; border-bottom: 1px solid #f9fafb; 
        text-decoration: none; color: #4b5563; transition: background 0.2s;
    }
    .resource-item:last-child { border-bottom: none; }
    .resource-item:hover { background-color: #f0fdf4; color: #007A33; }
    
    .item-left { display: flex; align-items: center; gap: 15px; }
    .icon-cours { color: #3b82f6; } 
    .icon-tp { color: #10b981; } 
    .icon-video { color: #ef4444; }
    
    .item-text { font-size: 15px; font-weight: 500; }
    .action-download { color: #9ca3af; transition: color 0.2s; }
    .resource-item:hover .action-download { color: #007A33; }
    .action-link { font-size: 13px; color: #2563eb; font-weight: 600; }
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

    <div style="position:relative;">
        <button class="burger-btn" onclick="toggleMenu()" title="Menu">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
        </button>

        <div id="dropdownMenu" class="dropdown-menu">
            <a href="student.php?m=<?= urlencode($matricule) ?>" class="dropdown-item item-blue">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                Retour aux matières
            </a>
            <a href="index.php" class="dropdown-item item-red">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                Déconnexion
            </a>
        </div>
    </div>
  </nav>

  <main class="main-content">
    <div class="header-row">
        <h1><?= htmlspecialchars($nomMatiere) ?></h1>
        <span class="badge">Matière active</span>
    </div>

    <div class="columns-wrapper">
        <div class="panel panel-cours">
            <div class="panel-header">Cours</div>
            <div class="panel-body">
                <?php if(empty($cours)): ?>
                    <div style="padding:25px; color:#9ca3af; font-size:14px; text-align:center;">Aucun cours disponible.</div>
                <?php else: foreach($cours as $c): ?>
                    <a href="<?= htmlspecialchars($c['filepath']) ?>" class="resource-item" target="_blank" download>
                        <div class="item-left">
                            <svg class="item-icon icon-cours" width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <span class="item-text"><?= htmlspecialchars($c['filename']) ?></span>
                        </div>
                        <svg class="action-download" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    </a>
                <?php endforeach; endif; ?>
            </div>
        </div>

        <div class="panel panel-tp">
            <div class="panel-header">TP & TD</div>
            <div class="panel-body">
                <?php if(empty($tps)): ?>
                    <div style="padding:25px; color:#9ca3af; font-size:14px; text-align:center;">Aucun TP disponible.</div>
                <?php else: foreach($tps as $t): ?>
                    <a href="<?= htmlspecialchars($t['filepath']) ?>" class="resource-item" target="_blank" download>
                        <div class="item-left">
                            <svg class="item-icon icon-tp" width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <span class="item-text"><?= htmlspecialchars($t['filename']) ?></span>
                        </div>
                        <svg class="action-download" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    </a>
                <?php endforeach; endif; ?>
            </div>
        </div>

        <div class="panel panel-video">
            <div class="panel-header">Vidéos</div>
            <div class="panel-body">
                <?php if(empty($videos)): ?>
                    <div style="padding:25px; color:#9ca3af; font-size:14px; text-align:center;">Aucune vidéo disponible.</div>
                <?php else: foreach($videos as $v): ?>
                    <a href="<?= htmlspecialchars($v['url']) ?>" class="resource-item" target="_blank">
                        <div class="item-left">
                            <svg class="item-icon icon-video" width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                            <span class="item-text"><?= htmlspecialchars($v['title'] ?: 'Vidéo sans titre') ?></span>
                        </div>
                        <span class="action-link">Voir</span>
                    </a>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </div>
  </main>

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