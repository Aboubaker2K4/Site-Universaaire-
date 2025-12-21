# Université - Application Web (PHP OOP + MySQL)

Petit projet universitaire fournissant trois espaces : Étudiant, Enseignant, Admin.

Installation rapide:

1. Importez `db.sql` dans MySQL :

```sql
CREATE DATABASE university; USE university;
-- puis exécuter le contenu de db.sql
```

2. Configurez la connexion DB dans `src/config.php`.
3. Placez le dossier `public` comme racine du serveur web (ex: Apache `DocumentRoot`).
4. Assurez-vous que `public/uploads` est écritable.

Stack: HTML, CSS, JS, PHP (OOP), MySQL.
