<?php
/**
 * ============================================================
 * Congreso Internacional de Tecnología e Innovación
 * ------------------------------------------------------------
 * Recibe el formulario del landing y guarda la inscripción.
 * Usa el patrón Post/Redirect/Get: procesa y redirige de vuelta
 * al landing con un mensaje de estado, para que si el alumno
 * recarga la página no se duplique el envío.
 * ============================================================
 */
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// --- 1. Verificar que todavía haya cupos ---
$resultado = $conexion->query('SELECT COUNT(*) AS total FROM inscritos');
$totalInscritos = (int) $resultado->fetch_assoc()['total'];

if ($totalInscritos >= CUPO_MAXIMO) {
    header('Location: index.php?status=agotado#inscripcion');
    exit;
}

// --- 2. Recoger y limpiar los datos del formulario ---
$nombres      = trim($_POST['nombres'] ?? '');
$apellidos    = trim($_POST['apellidos'] ?? '');
$email        = trim($_POST['email'] ?? '');
$documento    = trim($_POST['documento'] ?? '');
$institucion  = trim($_POST['institucion'] ?? '');
$telefono     = trim($_POST['telefono'] ?? '');
$ejeTematico  = trim($_POST['eje_tematico'] ?? '');

// --- 3. Validación server-side (nunca confíes solo en el "required" del HTML) ---
$camposObligatorios = compact('nombres', 'apellidos', 'email', 'documento', 'institucion', 'ejeTematico');
$hayVacios = false;
foreach ($camposObligatorios as $valor) {
    if ($valor === '') { $hayVacios = true; break; }
}

$emailValido = filter_var($email, FILTER_VALIDATE_EMAIL) !== false;

if ($hayVacios || !$emailValido) {
    header('Location: index.php?status=error#inscripcion');
    exit;
}

// --- 4. Guardar en la base de datos (consulta preparada = protección contra inyección SQL) ---
$sql = 'INSERT INTO inscritos (nombres, apellidos, email, documento, institucion, telefono, eje_tematico)
        VALUES (?, ?, ?, ?, ?, ?, ?)';
$stmt = $conexion->prepare($sql);
$stmt->bind_param('sssssss', $nombres, $apellidos, $email, $documento, $institucion, $telefono, $ejeTematico);

try {
    $stmt->execute();
    header('Location: index.php?status=ok#inscripcion');
} catch (mysqli_sql_exception $e) {
    // Código 1062 = entrada duplicada (el email ya tiene el índice UNIQUE en la tabla)
    if ($e->getCode() === 1062) {
        header('Location: index.php?status=duplicado#inscripcion');
    } else {
        header('Location: index.php?status=error#inscripcion');
    }
}

$stmt->close();
exit;
