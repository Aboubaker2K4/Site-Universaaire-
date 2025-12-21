<!-- php
require_once __DIR__ . '/../src/Student.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $nom = $_POST['nom'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $matricule = $_POST['matricule'] ?? '';
    $filiere = $_POST['filiere'] ?? 'Informatique';
    $annee = $_POST['annee'] ?? '';
    $s = new Student();
    if (!$s->exists($matricule)){
        $error = 'Matricule introuvable dans la base de données.';
    } else {
        header('Location: student.php?m=' . urlencode($matricule)); exit;
    }
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="assets/style.css"><title>Connexion Étudiant</title></head>
<body>
<div class="container">
  <div class="card">
    <h2>Connexion Étudiant</h2>
    php if($error): ?><div class="error">=htmlspecialchars($error)</div>php endif; ?>
    <form method="post">
      <input name="nom" placeholder="Nom">
      <input name="prenom" placeholder="Prénom">
      <input name="matricule" placeholder="Matricule">
      <select name="filiere"><option>Informatique</option></select>
      <input name="annee" placeholder="2024-2025">
      <button class="btn" type="submit">Entrer</button>
    </form>
  </div>
</div>
</body></html>
    --> 
<?php
// Fichier : public/student_form.php
require_once __DIR__ . '/../src/Student.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $matricule = trim($_POST['matricule'] ?? '');
    
    $s = new Student();
    
    // Vérification stricte (Nom + Prénom + Matricule)
    if ($s->checkLogin($matricule, $nom, $prenom)) {
        header('Location: student.php?m=' . urlencode($matricule)); 
        exit;
    } else {
        $error = "Identifiants incorrects. Vérifiez votre Matricule, Nom et Prénom.";
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Connexion Étudiant - EMSI</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <?php
    // Prioritize uploads/images/logo.png then uploads/logo.png
    $logoCandidates = ['uploads/images/logo.png', 'uploads/logo.png'];
    $localLogo = null;
    foreach ($logoCandidates as $c) { if (file_exists(__DIR__ . '/' . $c)) { $localLogo = $c; break; } }
    $logoSrc = $localLogo ? $localLogo : 'https://upload.wikimedia.org/wikipedia/commons/e/e8/Logo-emsi.png';

    // Background for student page
    $localStudentBg = 'uploads/images/backstudent.jpg';
    $bgSrc = (file_exists(__DIR__ . '/' . $localStudentBg) ? $localStudentBg : 'https://via.placeholder.com/1920x1080?text=Campus+EMSI');
    ?>

    <style>
    /* RESET */
    * { box-sizing: border-box; margin: 0; padding: 0; }
    
    body { 
        font-family: 'Inter', sans-serif; 
        color: #1f2937;
        /* FOND EMSI (spécifique étudiant) */
        background: linear-gradient(rgba(0, 80, 40, 0.85), rgba(0, 60, 30, 0.8)), 
                url('<?php echo $bgSrc; ?>');
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
    .burger-btn { background: none; border: none; cursor: pointer; padding: 8px; display: flex; align-items: center; justify-content: center; border-radius: 6px; transition: background 0.2s; }
    .burger-btn:hover { background-color: rgba(255,255,255,0.2); }

    .dropdown-menu { display: none; position: absolute; top: 70px; right: 40px; background: white; width: 220px; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); overflow: hidden; z-index: 100; border: 1px solid #e5e7eb; }
    .dropdown-item { display: flex; align-items: center; gap: 12px; padding: 15px 20px; text-decoration: none; color: #374151; font-weight: 600; font-size: 14px; transition: background 0.2s; }
    .dropdown-item:hover { background-color: #f3f4f6; color: #007A33; }
    .item-admin { color: #ef4444; border-top: 1px solid #f3f4f6; }
    .item-admin:hover { background-color: #fee2e2; color: #b91c1c; }

    /* CONTENU CENTRÉ */
    .main-content { flex: 1; display: flex; align-items: center; justify-content: center; padding: 20px; }

    /* CARTE FORMULAIRE */
    .form-card { 
        background: white; 
        width: 100%; max-width: 450px; 
        border-radius: 16px; 
        box-shadow: 0 20px 40px rgba(0,0,0,0.3); 
        overflow: hidden; 
        border-top: 6px solid #007A33; /* Bordure verte en haut */
    }
    
    .form-header { padding: 30px 30px 10px; text-align: center; }
    .form-header h2 { font-size: 1.8rem; color: #1f2937; font-weight: 800; margin: 0; }
    .form-header p { color: #6b7280; font-size: 0.9rem; margin-top: 5px; }

    .form-body { padding: 30px; }
    
    .form-group { margin-bottom: 20px; }
    label { display: block; font-size: 0.9rem; color: #374151; font-weight: 600; margin-bottom: 8px; }

    /* INPUTS (Style sombre conservé ou adapté) */
    /* J'ai mis un style clair ici pour aller avec le thème blanc/vert, plus lisible */
    input, select { 
        width: 100%; padding: 12px 16px; 
        background-color: #f9fafb; /* Gris très clair */
        color: #1f2937; 
        border: 2px solid #e5e7eb; 
        border-radius: 8px; 
        font-size: 0.95rem; outline: none; transition: border-color 0.2s; 
    }
    input:focus, select:focus { border-color: #007A33; background: white; } /* Focus Vert EMSI */

    /* BOUTON VERT EMSI */
    .btn-submit { 
        width: 100%; padding: 14px; 
        background-color: #007A33; 
        color: white; font-weight: 700; 
        border: none; border-radius: 8px; 
        cursor: pointer; font-size: 1rem; 
        margin-top: 10px; 
        transition: background 0.2s, transform 0.1s;
    }
    .btn-submit:hover { background-color: #006028; transform: translateY(-2px); }

    .error-msg { background-color: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem; text-align: center; border: 1px solid #fecaca; }
  </style>
</head>
<body>

  <nav class="navbar">
    <div class="brand">
        <img src="<?php echo $logoSrc; ?>" alt="Logo EMSI" class="logo-img">
        <div class="brand-text">
            <span class="brand-title">EMSI</span>
            <span class="brand-subtitle">PORTAIL ACADÉMIQUE</span>
        </div>
    </div>
    
    <div style="position: relative;">
        <button class="burger-btn" onclick="toggleMenu()" title="Menu">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
        </button>
        <div id="dropdownMenu" class="dropdown-menu">
            <a href="index.php" class="dropdown-item">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                Accueil
            </a>
            <a href="admin_form.php" class="dropdown-item item-admin">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                Administration
            </a>
        </div>
    </div>
  </nav>

  <main class="main-content">
    <div class="form-card">
      <div class="form-header">
          <h2>Espace Étudiant</h2>
          <p>Connectez-vous pour accéder à vos cours</p>
      </div>
      <div class="form-body">
        
        <?php if($error): ?>
          <div class="error-msg"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label>Nom</label>
                <input type="text" name="nom" placeholder="Votre nom" required>
            </div>
            
            <div class="form-group">
                <label>Prénom</label>
                <input type="text" name="prenom" placeholder="Votre prénom" required>
            </div>
            
            <div class="form-group">
                <label>Matricule</label>
                <input type="text" name="matricule" placeholder="ex: ETU001" required>
            </div>
            
            <div class="form-group">
                <label>Filière (Indicatif)</label>
                <select name="filiere">
                    <option value="1">Informatique</option>
                    <option value="2">Gestion</option>
                    <option value="3">Génie Civil</option>
                </select>
            </div>
            
            <button class="btn-submit" type="submit">Se connecter</button>
        </form>
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