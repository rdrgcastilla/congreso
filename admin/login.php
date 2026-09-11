<?php
/**
 * ============================================================
 * Panel de administración — Login
 * ============================================================
 */
require_once __DIR__ . '/../config.php';
session_start();

// Si ya inició sesión, no tiene sentido mostrarle el login de nuevo
if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario  = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($usuario === '' || $password === '') {
        $error = 'Completa usuario y contraseña.';
    } else {
        $stmt = $conexion->prepare('SELECT id, password_hash, nombre FROM admin_usuarios WHERE usuario = ?');
        $stmt->bind_param('s', $usuario);
        $stmt->execute();
        $admin = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        // password_verify compara el texto plano contra el hash guardado —
        // nunca se guarda ni se compara la contraseña en texto plano.
        if ($admin && password_verify($password, $admin['password_hash'])) {
            session_regenerate_id(true); // evita "session fixation"
            $_SESSION['admin_id']     = $admin['id'];
            $_SESSION['admin_nombre'] = $admin['nombre'];
            header('Location: dashboard.php');
            exit;
        }

        $error = 'Usuario o contraseña incorrectos.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Acceso administrador · CITIC 2026</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="admin-shell d-flex align-items-center">
<div class="container">
  <div class="login-card">
    <div class="text-center mb-4">
      <div class="eyebrow">CITIC 2026</div>
      <h2 class="section-title" style="font-size:1.6rem;">Panel de administración</h2>
      <p class="text-muted mb-0">Ingresa para ver el dashboard de inscritos.</p>
    </div>

    <?php if ($error): ?>
      <div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" novalidate>
      <div class="mb-3">
        <label for="usuario" class="form-label">Usuario</label>
        <input type="text" class="form-control" id="usuario" name="usuario" placeholder="admin" required autofocus>
      </div>
      <div class="mb-4">
        <label for="password" class="form-label">Contraseña</label>
        <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
      </div>
      <button type="submit" class="btn btn-naranja w-100">Ingresar</button>
    </form>

    <div class="text-center mt-4">
      <a href="../index.php" class="text-muted small"><i class="bi bi-arrow-left"></i> Volver al landing del congreso</a>
    </div>
  </div>
</div>
</body>
</html>
