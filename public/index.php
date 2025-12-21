<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>UniPortal - EMSI</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <?php
    // --- MODIFICATION ICI : Chemin exact de votre image ---
    $localBg = 'uploads/images/backindex.jpeg'; 

    // Gestion du Logo (Cherche d'abord le logo local, sinon utilise le lien web)
    $logoCandidates = ['uploads/images/logo.png', 'uploads/logo.png'];
    $localLogo = null;
    foreach ($logoCandidates as $c) {
        if (file_exists(__DIR__ . '/' . $c)) { $localLogo = $c; break; }
    }
    $logoSrc = $localLogo ? $localLogo : 'https://upload.wikimedia.org/wikipedia/commons/e/e8/Logo-emsi.png';
    
    // Gestion du Background
    // On vérifie si l'image existe pour l'afficher, sinon on garde juste le vert
    if (file_exists(__DIR__ . '/' . $localBg)) {
        $bgCss = "linear-gradient(rgba(0, 80, 40, 0.85), rgba(0, 60, 30, 0.8)), url('" . $localBg . "')";
    } else {
        $bgCss = "linear-gradient(rgba(0, 80, 40, 0.85), rgba(0, 60, 30, 0.8))";
    }
    ?>

    <style>
    /* RESET */
    * { box-sizing: border-box; margin: 0; padding: 0; }
    
    body { 
        font-family: 'Inter', sans-serif; 
        color: #1f2937;
        /* FOND : Image avec une superposition (overlay) verte pour le style EMSI */
        background: <?php echo $bgCss; ?>;
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    /* NAVBAR VERT EMSI */
    .navbar { 
        background-color: #007A33; /* Vert EMSI */
        height: 80px; 
        display: flex; 
        align-items: center; 
        justify-content: space-between; 
        padding: 0 40px; 
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        position: relative;
        z-index: 50;
    }

    .brand { display: flex; align-items: center; gap: 15px; text-decoration: none; }
    
    /* LOGO */
    .logo-img {
        height: 55px;
        background: white; 
        padding: 5px;
        border-radius: 6px;
    }

    .brand-text { display: flex; flex-direction: column; color: white; }
    .brand-title { font-weight: 800; font-size: 22px; letter-spacing: 1px; }
    .brand-subtitle { font-size: 11px; font-weight: 500; opacity: 0.9; text-transform: uppercase; }

    /* MENU BURGER */
    .burger-btn {
        background: none; border: none; cursor: pointer; padding: 8px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 6px; transition: background 0.2s;
    }
    .burger-btn:hover { background-color: rgba(255,255,255,0.2); }

    /* MENU DÉROULANT */
    .dropdown-menu {
        display: none; position: absolute; top: 70px; right: 40px;
        background: white; width: 220px; border-radius: 8px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        overflow: hidden; z-index: 100;
        border: 1px solid #e5e7eb;
    }
    .dropdown-item {
        display: flex; align-items: center; gap: 12px;
        padding: 15px 20px; text-decoration: none; 
        color: #374151; font-weight: 600; font-size: 14px;
        transition: background 0.2s;
    }
    .dropdown-item:hover { background-color: #f3f4f6; color: #007A33; }
    
    .item-admin { color: #ef4444; border-top: 1px solid #f3f4f6; }
    .item-admin:hover { background-color: #fee2e2; color: #b91c1c; }

    /* CONTENU PRINCIPAL */
    .main-content { 
        flex: 1; 
        display: flex; 
        flex-direction: column; 
        align-items: center; 
        justify-content: center; 
        padding: 20px;
        text-align: center;
    }

    .hero-title {
        color: white;
        font-size: 3.5rem;
        font-weight: 800;
        margin-bottom: 10px;
        text-shadow: 0 4px 15px rgba(0,0,0,0.3);
    }
    .hero-subtitle {
        color: #d1fae5;
        font-size: 1.4rem;
        margin-bottom: 60px;
        font-weight: 500;
        letter-spacing: 0.5px;
    }

    /* CARTES (Seulement 2 maintenant) */
    .cards-container { 
        display: flex; 
        gap: 40px; 
        flex-wrap: wrap; 
        justify-content: center; 
    }

    .role-card { 
        background: white; 
        width: 320px; 
        padding: 40px 30px; 
        border-radius: 16px; 
        text-align: center; 
        text-decoration: none; 
        color: #1f2937; 
        transition: transform 0.3s, box-shadow 0.3s;
        border-bottom: 6px solid transparent;
        box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    }

    .role-card:hover { 
        transform: translateY(-10px); 
        box-shadow: 0 25px 50px rgba(0,0,0,0.3); 
    }

    /* COULEURS DES CARTES */
    .card-student { border-bottom-color: #3b82f6; }
    .card-student:hover .icon-box { background: #eff6ff; color: #3b82f6; }
    
    .card-teacher { border-bottom-color: #007A33; }
    .card-teacher:hover .icon-box { background: #dcfce7; color: #007A33; }

    .icon-box { 
        width: 80px; height: 80px; 
        background: #f3f4f6; color: #4b5563; 
        border-radius: 50%; 
        display: flex; align-items: center; justify-content: center; 
        margin: 0 auto 25px; 
        transition: all 0.3s;
    }
    
    .role-title { font-size: 1.4rem; font-weight: 700; margin-bottom: 10px; }
    .role-desc { font-size: 0.95rem; color: #6b7280; line-height: 1.5; }
    
    .footer {
        padding: 20px;
        color: rgba(255,255,255,0.7);
        text-align: center;
        font-size: 0.85rem;
        border-top: 1px solid rgba(255,255,255,0.1);
    }
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
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="3" y1="12" x2="21" y2="12"></line>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
        </button>

        <div id="dropdownMenu" class="dropdown-menu">
            <a href="admin_form.php" class="dropdown-item item-admin">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                Administration
            </a>
        </div>
    </div>
  </nav>

  <main class="main-content">
    
    <h1 class="hero-title">Bienvenue à l'EMSI</h1>
    <p class="hero-subtitle">École Marocaine des Sciences de l'Ingénieur</p>

    <div class="cards-container">
        
        <a href="student_form.php" class="role-card card-student">
            <div class="icon-box">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg>
            </div>
            <div class="role-title">Espace Étudiant</div>
            <div class="role-desc">Accédez à vos cours, travaux pratiques et consultez vos résultats.</div>
        </a>

        <a href="teacher_form.php" class="role-card card-teacher">
            <div class="icon-box">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
            </div>
            <div class="role-title">Espace Enseignant</div>
            <div class="role-desc">Gérez vos matières, publiez du contenu et suivez vos classes.</div>
        </a>

        </div>
  </main>


  <script>
    function toggleMenu() {
        const menu = document.getElementById('dropdownMenu');
        menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
    }
    
    // Fermer le menu si on clique ailleurs
    window.onclick = function(event) {
        if (!event.target.closest('.burger-btn')) {
            document.getElementById('dropdownMenu').style.display = 'none';
        }
    }
  </script>

</body>
</html>