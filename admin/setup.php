<?php
declare(strict_types=1);
require __DIR__ . '/../lib/bootstrap.php';

$error = null;
$ready = false;
try {
    $server = db(true);
    $dbName = config()['db_name'];
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $dbName)) throw new RuntimeException('El nombre de la base de datos no es válido.');
    $server->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $schema = file_get_contents(PROJECT_ROOT . '/database/schema.sql');
    db()->exec($schema);
    $ready = true;
    $count = (int)db()->query('SELECT COUNT(*) FROM admin_users')->fetchColumn();
    if ($count > 0) {
        header('Location: login.php');
        exit;
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        verify_csrf();
        $name = trim((string)($_POST['name'] ?? ''));
        $email = strtolower(trim((string)($_POST['email'] ?? '')));
        $password = (string)($_POST['password'] ?? '');
        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 10) throw new RuntimeException('Completa los datos. La contraseña debe tener al menos 10 caracteres.');
        $stmt = db()->prepare('INSERT INTO admin_users (name,email,password_hash) VALUES (?,?,?)');
        $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
        flash('success', 'Administrador creado. Ya puedes iniciar sesión.');
        header('Location: login.php');
        exit;
    }
} catch (Throwable $e) {
    $error = $e->getMessage();
}
?>
<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Instalar panel | El Volcán</title><link rel="stylesheet" href="admin.css"></head><body class="auth-page"><main class="auth-card"><img src="../ferreteriaelvolcan/logotipo-ferreteria-volcan.png" alt="Ferretería El Volcán"><p class="kicker">Instalación inicial</p><h1>Crear administrador</h1><?php if ($error): ?><div class="alert error"><?=htmlspecialchars($error)?></div><?php endif; ?><?php if ($ready && !$error): ?><form method="post"><input type="hidden" name="csrf" value="<?=htmlspecialchars(csrf_token())?>"><label>Nombre<input name="name" required autocomplete="name"></label><label>Correo<input name="email" type="email" required autocomplete="email"></label><label>Contraseña<input name="password" type="password" minlength="10" required autocomplete="new-password"></label><button class="primary">Crear acceso seguro</button></form><?php else: ?><p>Revisa las credenciales MySQL en las variables de entorno o en <code>config.local.php</code>.</p><?php endif; ?></main></body></html>
