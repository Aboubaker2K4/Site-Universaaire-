<!--?php
// Fichier : public/student.php
require_once __DIR__ . '/../src/Student.php';
require_once __DIR__ . '/../src/Subject.php';

$matricule = $_GET['m'] ?? '';
if (!$matricule) { header('Location: index.php'); exit; }

$student = new Student();
// CORRECTION ICI : On utilise getInfo()
$info = $student->getInfo($matricule);

if (!$info) { die("Étudiant introuvable."); }

$subjectManager = new Subject();
// On récupère les matières de la filière de l'étudiant
$matieres = $subjectManager->getByFiliere($info['filiere_id']);
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Espace Étudiant - UniPortal</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    /* Style Global UniPortal */
    body { font-family: 'Inter', sans-serif; background: #f4f7fa; margin: 0; padding-bottom: 40px; }
    .navbar { background: #1e3a8a; height: 64px; display: flex; align-items: center; justify-content: space-between; padding: 0 24px; color: white; }
    .brand { font-weight: 700; font-size: 1.2rem; display: flex; gap: 10px; align-items: center; }
    .user-info { font-size: 0.9rem; background: rgba(255,255,255,0.1); padding: 5px 12px; border-radius: 20px; }
    
    .container { max-width: 1000px; margin: 30px auto; padding: 0 20px; }
    h1 { color: #1f2937; margin-bottom: 25px; }
    
    .matiere-block { background: white; border-radius: 12px; padding: 25px; margin-bottom: 30px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); border: 1px solid #e5e7eb; }
    .matiere-title { font-size: 1.5rem; color: #1e3a8a; margin-bottom: 20px; border-bottom: 2px solid #f3f4f6; padding-bottom: 10px; }
    
    .resources-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; }
    
    .section h3 { font-size: 1rem; color: #4b5563; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700; }
    
    .file-link { 
        display: block; padding: 12px; background: #f9fafb; border: 1px solid #e5e7eb; 
        border-radius: 6px; text-decoration: none; color: #374151; margin-bottom: 8px; 
        transition: all 0.2s; font-size: 0.95rem;
    }
    .file-link:hover { background: #eef2ff; border-color: #c7d2fe; color: #1e3a8a; padding-left: 18px; }
    
    /* Couleurs des sections */
    .sec-cours { border-left: 4px solid #3b82f6; padding-left: 15px; }
    .sec-tp { border-left: 4px solid #10b981; padding-left: 15px; }
    .sec-video { border-left: 4px solid #f59e0b; padding-left: 15px; }
    
    .btn-retour { background: #dc2626; color: white; padding: 8px 16px; text-decoration: none; border-radius: 6px; font-size: 0.9rem; }
  </style>
</head>
<body>
  <nav class="navbar">
    <div class="brand"><span>UP</span> UniPortal</div>
    <div style="display:flex; gap:15px; align-items:center;">
        <span class="user-info">👤 ?= htmlspecialchars($info['nom'] . ' ' . $info['prenom']) ?></span>
        <a href="index.php" class="btn-retour">Déconnexion</a>
    </div>
  </nav>

  <div class="container">
    <h1>Mes Cours & Ressources</h1>
    
    ?php if (empty($matieres)): ?>
        <p>Aucune matière n'est assignée à votre filière pour le moment.</p>
    ?php else: ?>
        <php foreach ($matieres as $matiere): ?>
            ?php 
                // Récupération des ressources via Subject.php mis à jour
                $cours = $subjectManager->getCourses($matiere['id']);
                $tps = $subjectManager->getTPs($matiere['id']);
                $videos = $subjectManager->getVideos($matiere['id']);
            ?>
            <div class="matiere-block">
                <div class="matiere-title">?= htmlspecialchars($matiere['nom']) ?></div>
                
                <div class="resources-grid">
                    <div class="section sec-cours">
                        <h3>📄 Cours (PDF)</h3>
                        <php if(empty($cours)) echo "<p style='color:#9ca3af; font-size:0.9rem;'>Aucun cours</p>"; ?>
                        ?php foreach ($cours as $c): ?>
                            <a href="?= htmlspecialchars($c['filepath']) ?>" class="file-link" target="_blank">
                                ?= htmlspecialchars($c['filename']) ?>
                            </a>
                    <php endforeach; ?>
                    </div>

                    <div class="section sec-tp">
                        <h3>🧪 Travaux Pratiques</h3>
                        ?php if(empty($tps)) echo "<p style='color:#9ca3af; font-size:0.9rem;'>Aucun TP</p>"; ?>
                        ?php foreach ($tps as $t): ?>
                            <a href="<= htmlspecialchars($t['filepath']) ?>" class="file-link" target="_blank">
                                <= htmlspecialchars($t['filename']) ?>
                            </a>
                        <php endforeach; ?>
                    </div>

                    <div class="section sec-video">
                        <h3>🎬 Vidéos</h3>
                        <php if(empty($videos)) echo "<p style='color:#9ca3af; font-size:0.9rem;'>Aucune vidéo</p>"; ?>
                        <php foreach ($videos as $v): ?>
                            <a href="<= htmlspecialchars($v['url']) ?>" class="file-link" target="_blank">
                                📺 <= htmlspecialchars($v['title'] ?: 'Voir la vidéo') ?>
                            </a>
                        <php endforeach; ?>
                    </div>
                </div>
            </div>
        <php endforeach; ?>
    <php endif; ?>
  </div>
</body>
</html>
                        --> 
<?php
require_once __DIR__ . '/../src/Student.php';
require_once __DIR__ . '/../src/Subject.php';

// Vérification de sécurité
$matricule = $_GET['m'] ?? '';
if (!$matricule) { header('Location: student_form.php'); exit; }

$student = new Student();
$info = $student->getInfo($matricule);
if (!$info) die("Étudiant introuvable.");

// Récupération des matières
$subjectManager = new Subject();
$matieres = $subjectManager->getByFiliere($info['filiere_id']);
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Mes Matières - EMSI</title>
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
        flex-shrink: 0; /* La navbar ne doit pas rétrécir */
    }

    .brand { display: flex; align-items: center; gap: 15px; text-decoration: none; }
    .logo-img { height: 55px; background: white; padding: 5px; border-radius: 6px; }
    .brand-text { display: flex; flex-direction: column; color: white; }
    .brand-title { font-weight: 800; font-size: 22px; letter-spacing: 1px; }
    .brand-subtitle { font-size: 11px; font-weight: 500; opacity: 0.9; text-transform: uppercase; }

    .user-area { display: flex; align-items: center; gap: 20px; }
    .user-info { color: white; font-size: 15px; font-weight: 600; text-shadow: 0 1px 2px rgba(0,0,0,0.2); }

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
        padding: 15px 20px; text-decoration: none; font-weight: 600; font-size: 14px; transition: background 0.1s;
    }
    .dropdown-item:hover { background-color: #f3f4f6; }
    .item-red { color: #ef4444; border-top: 1px solid #f3f4f6; }
    .item-red:hover { background-color: #fee2e2; }

    /* --- CENTRAGE VERTICAL ET HORIZONTAL --- */
    .main-content {
        flex: 1; /* Prend tout l'espace restant */
        display: flex;
        flex-direction: column;
        justify-content: center; /* Centre Verticalement */
        align-items: center;     /* Centre Horizontalement */
        padding: 40px 20px;
        width: 100%;
    }

    .page-title { 
        color: white; 
        font-size: 2.5rem; /* Plus grand */
        font-weight: 800; 
        text-align: center; 
        margin-bottom: 50px; 
        text-shadow: 0 4px 10px rgba(0,0,0,0.4);
    }

    .cards-wrapper {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 40px; /* Plus d'espace entre les cartes */
        width: 100%;
        max-width: 1400px;
    }

    /* CARTES */
    .card { 
        background: white; 
        width: 320px; 
        border-radius: 16px; 
        padding: 40px 30px; 
        box-shadow: 0 20px 40px rgba(0,0,0,0.3); /* Ombre plus prononcée */
        display: flex; 
        flex-direction: column;
        align-items: center; 
        gap: 25px; 
        text-decoration: none; 
        color: #1f2937; 
        transition: transform 0.3s, box-shadow 0.3s;
        border-bottom: 6px solid #007A33;
    }
    .card:hover { 
        transform: translateY(-10px); 
        box-shadow: 0 25px 50px rgba(0,0,0,0.4); 
        border-bottom-color: #005c26;
    }

    .icon-box { 
        width: 80px; height: 80px; 
        background-color: #f0fdf4; 
        color: #007A33; 
        border-radius: 50%; 
        display: flex; align-items: center; justify-content: center; 
        transition: background 0.3s;
    }
    .card:hover .icon-box { background-color: #dcfce7; }

    .card-title { font-weight: 700; font-size: 1.2rem; text-align: center; }

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
        <span class="user-info">Bonjour, <?= htmlspecialchars($info['prenom']) ?></span>
        
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

  <main class="main-content">
    <h1 class="page-title">Mes Matières</h1>

    <div class="cards-wrapper">
        <?php foreach ($matieres as $m): ?>
            <a href="subject_details.php?id=<?= $m['id'] ?>&m=<?= urlencode($matricule) ?>" class="card">
                <div class="icon-box">
                    <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                </div>
                <div class="card-title"><?= htmlspecialchars($m['nom']) ?></div>
            </a>
        <?php endforeach; ?>
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