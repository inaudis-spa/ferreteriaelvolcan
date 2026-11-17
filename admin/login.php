<?php
declare(strict_types=1);
require __DIR__ . '/../lib/bootstrap.php';
start_secure_session();
try {
    if ((int)db()->query('SELECT COUNT(*) FROM admin_users')->fetchColumn() === 0) { header('Location: setup.php'); exit; }
} catch (Throwable) { header('Location: setup.php'); exit; }
if (admin_user()) { header('Location: index.php'); exit; }
$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $stmt = db()->prepare('SELECT * FROM admin_users WHERE email = ? AND active = 1');
    $stmt->execute([strtolower(trim((string)($_POST['email'] ?? '')))]);
    $user = $stmt->fetch();
    if ($user && password_verify((string)($_POST['password'] ?? ''), $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $user['id'];
        header('Location: ../index.html'); exit;
    }
    $error = 'Correo o contraseña incorrectos.';
}
$flashes = take_flashes();
?>
<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Administración | El Volcán</title><link rel="stylesheet" href="admin.css"></head><body class="auth-page"><main class="auth-card"><img src="../ferreteriaelvolcan/logotipo-ferreteria-volcan.png" alt="Ferretería El Volcán"><p class="kicker">Panel privado</p><h1>Administración</h1><?php foreach($flashes as $flash): ?><div class="alert <?=$flash['type']?>"><?=htmlspecialchars($flash['message'])?></div><?php endforeach; ?><?php if($error): ?><div class="alert error"><?=htmlspecialchars($error)?></div><?php endif; ?><form method="post"><input type="hidden" name="csrf" value="<?=htmlspecialchars(csrf_token())?>"><label>Correo<input name="email" type="email" required autocomplete="email"></label><label>Contraseña<input name="password" type="password" required autocomplete="current-password"></label><button class="primary">Ingresar</button></form><a class="back" href="../index.html">← Volver al sitio</a></main></body></html>
