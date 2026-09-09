<?php
session_start();

require_once __DIR__ . '/../includes/db.php';

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Vul je e-mailadres en wachtwoord in.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([
            'email' => $email
        ]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];

            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Ongeldige login gegevens.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inloggen - Tuinman Piet</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="admin-login-page">

    <main class="login-wrapper">
        <section class="login-card">
            <h1>Inloggen</h1>
            <p>Beheeromgeving Tuinman Piet</p>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="email">E-mailadres</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        required
                        autocomplete="email"
                    >
                </div>

                <div class="form-group">
                    <label for="password">Wachtwoord</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required
                        autocomplete="current-password"
                    >
                </div>

                <button type="submit" class="btn btn-primary">
                    Inloggen
                </button>
            </form>
        </section>
    </main>

</body>
</html>