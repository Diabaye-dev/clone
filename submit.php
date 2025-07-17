<?php
$dsn = 'mysql:host=localhost;dbname=membership;charset=utf8mb4';
$user = 'root';
$pass = 'password';

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("CREATE TABLE IF NOT EXISTS members (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(100),
        password VARCHAR(255),
        nom VARCHAR(100),
        prenom VARCHAR(100),
        email VARCHAR(255),
        telephone VARCHAR(100),
        nationalite VARCHAR(100),
        adresse TEXT,
        type_adhesion VARCHAR(50),
        cv_filename VARCHAR(255),
        id_filename VARCHAR(255),
        blocked TINYINT(1) DEFAULT 1
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $uploadDir = __DIR__ . '/uploads';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $errors = [];
    $required = ['nom','prenom','email','telephone','nationalite','adresse','type_adhesion'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            $errors[] = "Le champ $field est requis";
        }
    }
    if (empty($_FILES['cv']['name'])) {
        $errors[] = 'Le CV est requis';
    }
    if (empty($_FILES['piece_id']['name'])) {
        $errors[] = "La pièce d'identité est requise";
    }
    if (!isset($_POST['terms'])) {
        $errors[] = 'Vous devez accepter les conditions';
    }

    if ($errors) {
        echo "<!DOCTYPE html><html lang='fr'><head><meta charset='UTF-8'><title>Erreurs</title>"
            . "<link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css'></head><body><div class='container mt-5'>";
        echo "<div class='alert alert-danger'><ul>";
        foreach ($errors as $e) {
            echo '<li>' . htmlspecialchars($e) . '</li>'; 
        }
        echo "</ul></div><a href='membership_form.html' class='btn btn-secondary'>Retour</a></div></body></html>";
        exit;
    }

    $cvFilename = null;
    if (!empty($_FILES['cv']['name'])) {
        $cvFilename = basename($_FILES['cv']['name']);
        move_uploaded_file($_FILES['cv']['tmp_name'], "$uploadDir/$cvFilename");
    }

    $idFilename = null;
    if (!empty($_FILES['piece_id']['name'])) {
        $idFilename = basename($_FILES['piece_id']['name']);
        move_uploaded_file($_FILES['piece_id']['tmp_name'], "$uploadDir/$idFilename");
    }

    $stmt = $pdo->prepare("INSERT INTO members (
        username, password, nom, prenom, email, telephone, nationalite, adresse,
        type_adhesion, cv_filename, id_filename
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $telephone = trim($_POST['telephone'] ?? '');

    $stmt->execute([
        null,
        null,
        $_POST['nom'] ?? null,
        $_POST['prenom'] ?? null,
        $_POST['email'] ?? null,
        $telephone,
        $_POST['nationalite'] ?? null,
        $_POST['adresse'] ?? null,
        $_POST['type_adhesion'] ?? null,
        $cvFilename,
        $idFilename
    ]);

    header('Location: membership_form.html');
    exit;
} catch (PDOException $e) {
    echo 'Database error: ' . $e->getMessage();
}
?>
