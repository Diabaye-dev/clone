<?php
$dsn = 'mysql:host=localhost;dbname=membership;charset=utf8mb4';
$user = 'root';
$pass = 'password';

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_GET['delete'])) {
        $stmt = $pdo->prepare('DELETE FROM members WHERE id = ?');
        $stmt->execute([$_GET['delete']]);
        header('Location: admin.php');
        exit;
    }

    if (isset($_GET['block'])) {
        $stmt = $pdo->prepare('UPDATE members SET blocked = 1 - blocked WHERE id = ?');
        $stmt->execute([$_GET['block']]);
        header('Location: admin.php');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_id'])) {
        $sql = 'UPDATE members SET username = ?, nom = ?, prenom = ?, email = ?, telephone = ?, nationalite = ?, adresse = ?, type_adhesion = ?';
        $params = [
            $_POST['username'] ?? null,
            $_POST['nom'] ?? null,
            $_POST['prenom'] ?? null,
            $_POST['email'] ?? null,
            $_POST['telephone'] ?? null,
            $_POST['nationalite'] ?? null,
            $_POST['adresse'] ?? null,
            $_POST['type_adhesion'] ?? null
        ];

        if (!empty($_POST['password'])) {
            $sql .= ', password = ?';
            $params[] = password_hash($_POST['password'], PASSWORD_DEFAULT);
        }

        $sql .= ' WHERE id = ?';
        $params[] = $_POST['update_id'];

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        header('Location: admin.php');
        exit;
    }

    $editMember = null;
    if (isset($_GET['edit'])) {
        $stmt = $pdo->prepare('SELECT * FROM members WHERE id = ?');
        $stmt->execute([$_GET['edit']]);
        $editMember = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    $members = $pdo->query('SELECT * FROM members ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Database error: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Administration des inscriptions</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4">Administration des inscriptions</h1>
    <?php if ($editMember): ?>
    <h2 class="h4">Modifier le membre #<?= htmlspecialchars($editMember['id']) ?></h2>
    <form method="post" action="admin.php" class="row g-3 mb-4">
        <input type="hidden" name="update_id" value="<?= htmlspecialchars($editMember['id']) ?>">
        <div class="col-md-4">
            <label class="form-label">Nom d'utilisateur
                <input type="text" class="form-control" name="username" value="<?= htmlspecialchars($editMember['username']) ?>">
            </label>
        </div>
        <div class="col-md-4">
            <label class="form-label">Mot de passe (laisser vide pour conserver)
                <input type="password" class="form-control" name="password">
            </label>
        </div>
        <div class="col-md-4">
            <label class="form-label">Nom
                <input type="text" class="form-control" name="nom" value="<?= htmlspecialchars($editMember['nom']) ?>">
            </label>
        </div>
        <div class="col-md-4">
            <label class="form-label">Prénom
                <input type="text" class="form-control" name="prenom" value="<?= htmlspecialchars($editMember['prenom']) ?>">
            </label>
        </div>
        <div class="col-md-4">
            <label class="form-label">Email
                <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($editMember['email']) ?>">
            </label>
        </div>
        <div class="col-md-4">
            <label class="form-label">Téléphone
                <input type="text" class="form-control" name="telephone" value="<?= htmlspecialchars($editMember['telephone']) ?>">
            </label>
        </div>
        <div class="col-md-4">
            <label class="form-label">Nationalité
                <input type="text" class="form-control" name="nationalite" value="<?= htmlspecialchars($editMember['nationalite']) ?>">
            </label>
        </div>
        <div class="col-md-8">
            <label class="form-label">Adresse
                <textarea class="form-control" name="adresse" rows="2"><?= htmlspecialchars($editMember['adresse']) ?></textarea>
            </label>
        </div>
        <div class="col-md-4">
            <label class="form-label">Type adhésion
                <input type="text" class="form-control" name="type_adhesion" value="<?= htmlspecialchars($editMember['type_adhesion']) ?>">
            </label>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-success">Enregistrer</button>
        </div>
    </form>
    <?php endif; ?>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Email</th>
                <th>Téléphone</th>
                <th>Nationalité</th>
                <th>Adresse</th>
                <th>Type adhésion</th>
                <th>Bloqué</th>
                <th>CV</th>
                <th>Pièce ID</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($members as $m): ?>
            <tr>
                <td><?= htmlspecialchars($m['id']) ?></td>
                <td><?= htmlspecialchars($m['username']) ?></td>
                <td><?= htmlspecialchars($m['nom']) ?></td>
                <td><?= htmlspecialchars($m['prenom']) ?></td>
                <td><?= htmlspecialchars($m['email']) ?></td>
                <td><?= htmlspecialchars($m['telephone']) ?></td>
                <td><?= htmlspecialchars($m['nationalite']) ?></td>
                <td><?= nl2br(htmlspecialchars($m['adresse'])) ?></td>
                <td><?= htmlspecialchars($m['type_adhesion']) ?></td>
                <td><?= $m['blocked'] ? 'Oui' : 'Non' ?></td>
                <td><?php if ($m['cv_filename']): ?><a href="uploads/<?= urlencode($m['cv_filename']) ?>">CV</a><?php endif; ?></td>
                <td><?php if ($m['id_filename']): ?><a href="uploads/<?= urlencode($m['id_filename']) ?>">ID</a><?php endif; ?></td>
                <td>
                    <a href="admin.php?edit=<?= urlencode($m['id']) ?>">Éditer</a> |
                    <a href="admin.php?block=<?= urlencode($m['id']) ?>">
                        <?= $m['blocked'] ? 'Débloquer' : 'Bloquer' ?>
                    </a> |
                    <a class="text-danger" href="admin.php?delete=<?= urlencode($m['id']) ?>" onclick="return confirm('Supprimer ce membre ?');">Supprimer</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
