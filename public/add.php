<?php
require_once __DIR__ . '/../src/Student.php';
require_once __DIR__ . '/../src/Teacher.php';
$msg='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $type = $_POST['type'] ?? '';
  if($type==='etudiant'){
    $s = new Student();
    if($s->exists($_POST['matricule'])) $msg='Matricule déjà existant';
    else { $s->create($_POST); $msg='Étudiant ajouté'; }
  } else {
    $t = new Teacher();
    if($t->exists($_POST['matricule'])) $msg='Matricule déjà existant';
    else { $t->create(['matricule'=>$_POST['matricule'],'nom'=>$_POST['nom'],'prenom'=>$_POST['prenom'],'matiere_id'=>intval($_POST['matiere_id'])]); $msg='Enseignant ajouté'; }
  }
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="assets/style.css"><title>Ajouter</title></head>
<body>
<div class="container">
  <div class="card">
    <h2>Ajouter Utilisateur</h2>
    <?php if($msg): ?><div><?=htmlspecialchars($msg)?></div><?php endif; ?>
    <form method="post">
      <select name="type"><option value="etudiant">Étudiant</option><option value="enseignant">Enseignant</option></select>
      <input name="matricule" placeholder="Matricule / ID">
      <input name="nom" placeholder="Nom">
      <input name="prenom" placeholder="Prénom">
      <input name="filiere" placeholder="Filière">
      <input name="annee" placeholder="Année">
      <input name="matiere_id" placeholder="ID Matière (pour enseignant)">
      <button class="btn" type="submit">Enregistrer</button>
    </form>
  </div>
</div>
</body></html>
