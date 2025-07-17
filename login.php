<?php
session_start();
$dsn = 'mysql:host=localhost;dbname=membership;charset=utf8mb4';
$user = 'root';
$pass = 'password';

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $error = null;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $stmt = $pdo->prepare('SELECT * FROM members WHERE username = ?');
        $stmt->execute([$_POST['username'] ?? '']);
        $member = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($member && !$member['blocked'] && password_verify($_POST['password'] ?? '', $member['password'])) {
            $_SESSION['member_id'] = $member['id'];
            header('Location: member.php');
            exit;
        } else {
            $error = "Identifiants incorrects ou compte inactif";
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
    <title>Connexion membre</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5" style="max-width:400px;">
    <h1 class="mb-4 h3">Connexion membre</h1>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" class="mb-3">
        <div class="mb-3">
            <label class="form-label" for="username">Nom d'utilisateur</label>
            <input type="text" name="username" id="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label" for="password">Mot de passe</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Se connecter</button>
    </form>
    <a href="membership_form.html">Retour au formulaire d'inscription</a>
</div>
</body>
</html>
