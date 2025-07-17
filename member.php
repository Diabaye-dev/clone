<?php
session_start();
if (!isset($_SESSION['member_id'])) {
    header('Location: login.php');
    exit;
}
$dsn = 'mysql:host=localhost;dbname=membership;charset=utf8mb4';
$user = 'root';
$pass = 'password';

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->prepare('SELECT * FROM members WHERE id = ?');
    $stmt->execute([$_SESSION['member_id']]);
    $member = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$member || $member['blocked']) {
        header('Location: logout.php');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $updates = [];
        $params = [];
        if (isset($_POST['username']) && $_POST['username'] !== '') {
            $updates[] = 'username = ?';
            $params[] = $_POST['username'];
        }
        if (isset($_POST['password']) && $_POST['password'] !== '') {
            $updates[] = 'password = ?';
            $params[] = password_hash($_POST['password'], PASSWORD_DEFAULT);
        }
        if ($updates) {
            $sql = 'UPDATE members SET ' . implode(', ', $updates) . ' WHERE id = ?';
            $params[] = $member['id'];
            $up = $pdo->prepare($sql);
            $up->execute($params);
            header('Location: member.php');
            exit;
        }
    }
} catch (PDOException $e) {
    die('Database error: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Espace membre</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5" style="max-width:500px;">
    <h1 class="mb-4 h3">Bienvenue <?= htmlspecialchars($member['prenom']) ?></h1>
    <form method="post" class="mb-3">
        <div class="mb-3">
            <label class="form-label" for="username">Nom d'utilisateur</label>
            <input type="text" id="username" name="username" class="form-control" value="<?= htmlspecialchars($member['username']) ?>">
        </div>
        <div class="mb-3">
            <label class="form-label" for="password">Nouveau mot de passe</label>
            <input type="password" id="password" name="password" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Mettre à jour</button>
    </form>
    <a href="logout.php" class="btn btn-link">Se déconnecter</a>
</div>
</body>
</html>
