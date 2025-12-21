<?php
require_once __DIR__ . '/../src/Student.php';
require_once __DIR__ . '/../src/Teacher.php';
$type = $_GET['type'] ?? '';
$key = $_GET['key'] ?? '';
if ($type==='etudiant'){
  $s = new Student();
  $row = $s->getInfo($matricule);
} else {
  $t = new Teacher();
  $row = $t->getInfo($matricule);
}
if (!$row) { echo 'Introuvable'; exit; }
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['delete'])){
  if($type==='etudiant'){
    $db = (require_once __DIR__ . '/../src/Database.php');
  }
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="assets/style.css"><title>Infos</title></head>
<body>
<div class="container">
  <div class="card">
    <h2>Informations</h2>
    <?php foreach($row as $k=>$v): ?>
      <div><strong><?=htmlspecialchars($k)?>:</strong> <?=htmlspecialchars($v)?></div>
    <?php endforeach; ?>
    <form method="post" onsubmit="return confirm('Confirmer suppression ?');">
      <button class="btn secondary" name="delete">Supprimer</button>
    </form>
  </div>
</div>
</body></html>
