<?php
/**
 * ============================================================
 * Congreso Internacional de Tecnología e Innovación
 * en Ingeniería y Computación
 * ------------------------------------------------------------
 * Landing pública + formulario de inscripción.
 * ============================================================
 */
require_once __DIR__ . '/config.php';

// --- Cupos disponibles: se calcula en cada carga, contra la base ---
$totalInscritos = 0;
$resultado = $conexion->query('SELECT COUNT(*) AS total FROM inscritos');
if ($resultado) {
    $totalInscritos = (int) $resultado->fetch_assoc()['total'];
}
$cuposDisponibles = max(0, CUPO_MAXIMO - $totalInscritos);
$cuposAgotados = $cuposDisponibles === 0;

// --- Mensaje de estado luego de procesar el formulario (patrón Post/Redirect/Get) ---
$status = $_GET['status'] ?? null;
$mensajes = [
    'ok'       => ['tipo' => 'success', 'texto' => '¡Listo! Tu inscripción se registró correctamente. Te contactaremos por correo con los detalles del congreso.'],
    'error'    => ['tipo' => 'danger',  'texto' => 'Hubo un problema con los datos enviados. Revisa el formulario e inténtalo de nuevo.'],
    'duplicado'=> ['tipo' => 'warning', 'texto' => 'Ese correo ya está registrado. Si crees que es un error, escríbenos.'],
    'agotado'  => ['tipo' => 'warning', 'texto' => 'Lo sentimos, los cupos para el congreso ya se agotaron.'],
];
$mensaje = $mensajes[$status] ?? null;

$ejesTematicos = [
    ['icono' => 'bi-cpu',            'nombre' => 'Inteligencia Artificial'],
    ['icono' => 'bi-shield-lock',    'nombre' => 'Ciberseguridad'],
    ['icono' => 'bi-code-slash',     'nombre' => 'Desarrollo de Software'],
    ['icono' => 'bi-broadcast-pin',  'nombre' => 'Internet de las Cosas (IoT)'],
    ['icono' => 'bi-diagram-3',      'nombre' => 'Redes y Telecomunicaciones'],
    ['icono' => 'bi-vr',             'nombre' => 'Realidad Virtual y Aumentada'],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Congreso Internacional de Tecnología e Innovación · CITIC 2027</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<!-- ===================== NAVBAR ===================== -->
<nav class="navbar navbar-expand-lg navbar-congreso py-3">
  <div class="container">
    <a class="navbar-brand navbar-brand-congreso" href="#inicio">
      CITIC <span class="badge-acento">2026</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-4">
        <li class="nav-item"><a class="nav-link nav-link-congreso" href="#sobre">Sobre el congreso</a></li>
        <li class="nav-item"><a class="nav-link nav-link-congreso" href="#ejes">Ejes temáticos</a></li>
        <li class="nav-item"><a class="nav-link nav-link-congreso" href="admin/login.php">Acceso administrador</a></li>
        <li class="nav-item mt-2 mt-lg-0">
          <a class="btn btn-naranja" href="#inscripcion">Inscríbete</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- ===================== HERO ===================== -->
<header class="hero-congreso" id="inicio">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-8">
        <span class="hero-eyebrow"><i class="bi bi-stars"></i> Programación de Dispositivos Móviles · USIL</span>
        <h1 class="hero-title">
          Congreso Internacional de <span class="acento">Tecnología e Innovación</span> en Ingeniería y Computación
        </h1>
        <p class="hero-sub">Tres días de charlas, talleres y casos reales sobre inteligencia artificial, ciberseguridad, desarrollo de software y las tendencias que están moviendo la industria.</p>
        <div class="hero-meta">
          <span><i class="bi bi-calendar-event"></i> 18–20 de noviembre, 2026</span>
          <span><i class="bi bi-geo-alt"></i> Campus USIL, La Molina · Lima</span>
          <span><i class="bi bi-camera-video"></i> Presencial con transmisión híbrida</span>
        </div>
        <div class="mt-4">
          <a href="#inscripcion" class="btn btn-naranja btn-lg me-2">
            <?= $cuposAgotados ? 'Ver detalles' : 'Reservar mi cupo' ?>
          </a>
          <a href="#sobre" class="btn btn-azul-outline btn-lg" style="border-color:rgba(255,255,255,.4); color:#eaf1ff;">Conocer más</a>
        </div>
      </div>
    </div>
  </div>
</header>

<!-- ===================== STATS FLOTANTES ===================== -->
<div class="container">
  <div class="row g-3 stat-card-float">
    <div class="col-md-4">
      <div class="stat-card">
        <div class="stat-value"><?= CUPO_MAXIMO ?></div>
        <div class="stat-label">Cupos totales para el congreso</div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="stat-card acento">
        <div class="stat-value"><?= $cuposDisponibles ?></div>
        <div class="stat-label">Cupos disponibles ahora mismo</div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="stat-card">
        <div class="stat-value"><?= $totalInscritos ?></div>
        <div class="stat-label">Personas ya inscritas</div>
      </div>
    </div>
  </div>
</div>

<!-- ===================== SOBRE EL CONGRESO ===================== -->
<section class="container py-5 mt-4" id="sobre">
  <span class="eyebrow">Sobre el congreso</span>
  <h2 class="section-title">Tres días pensados para llevarte algo concreto</h2>
  <p class="section-sub mb-4">No es solo teoría: cada bloque combina una charla con un caso real y un espacio práctico para aplicar lo aprendido.</p>
  <div class="row g-4">
    <div class="col-md-4">
      <div class="feature-card">
        <div class="feature-icon"><i class="bi bi-mic"></i></div>
        <h5>Ponentes de la industria</h5>
        <p class="text-muted mb-0">Especialistas de empresas tech de la región compartiendo casos reales, no solo diapositivas.</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="feature-card">
        <div class="feature-icon"><i class="bi bi-tools"></i></div>
        <h5>Talleres prácticos</h5>
        <p class="text-muted mb-0">Sesiones hands-on donde construyes algo funcional, no solo lo ves en pantalla.</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="feature-card">
        <div class="feature-icon"><i class="bi bi-patch-check"></i></div>
        <h5>Certificación</h5>
        <p class="text-muted mb-0">Certificado de participación al completar el congreso, válido como horas de actualización.</p>
      </div>
    </div>
  </div>
</section>

<!-- ===================== EJES TEMÁTICOS ===================== -->
<section class="container py-5" id="ejes">
  <span class="eyebrow">Ejes temáticos</span>
  <h2 class="section-title">Elige el eje que más te interesa al inscribirte</h2>
  <div class="row g-3 mt-2">
    <?php foreach ($ejesTematicos as $eje): ?>
    <div class="col-sm-6 col-lg-4">
      <div class="eje-chip">
        <i class="bi <?= $eje['icono'] ?>"></i>
        <?= htmlspecialchars($eje['nombre']) ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- ===================== FORMULARIO DE INSCRIPCIÓN ===================== -->
<section class="container py-5" id="inscripcion">
  <div class="form-section">
    <div class="row">
      <div class="col-lg-6">
        <span class="eyebrow" style="color:#8fa3d0;">Inscripción</span>
        <h2 class="section-title" style="color:#eaf1ff;">Asegura tu lugar en el congreso</h2>
        <p style="color:#a9bde3;">La inscripción es gratuita. Solo necesitamos algunos datos para gestionar tu acceso y tu certificado.</p>

        <?php if ($mensaje): ?>
          <div class="alert alert-<?= $mensaje['tipo'] ?> mt-3" role="alert">
            <?= htmlspecialchars($mensaje['texto']) ?>
          </div>
        <?php endif; ?>

        <?php if ($cuposAgotados): ?>
          <div class="alert alert-warning mt-3">
            <i class="bi bi-exclamation-triangle"></i> Los cupos para este congreso ya se agotaron. ¡Gracias por tu interés!
          </div>
        <?php endif; ?>
      </div>

      <div class="col-lg-6">
        <form action="procesar_inscripcion.php" method="POST" novalidate>
          <div class="row g-3">
            <div class="col-sm-6">
              <label for="nombres">Nombres</label>
              <input type="text" class="form-control" id="nombres" name="nombres" placeholder="Valentina" required <?= $cuposAgotados ? 'disabled' : '' ?>>
            </div>
            <div class="col-sm-6">
              <label for="apellidos">Apellidos</label>
              <input type="text" class="form-control" id="apellidos" name="apellidos" placeholder="Ramírez" required <?= $cuposAgotados ? 'disabled' : '' ?>>
            </div>
            <div class="col-sm-6">
              <label for="email">Correo electrónico</label>
              <input type="email" class="form-control" id="email" name="email" placeholder="tucorreo@ejemplo.com" required <?= $cuposAgotados ? 'disabled' : '' ?>>
            </div>
            <div class="col-sm-6">
              <label for="documento">DNI / Carné de extranjería</label>
              <input type="text" class="form-control" id="documento" name="documento" placeholder="72345678" required <?= $cuposAgotados ? 'disabled' : '' ?>>
            </div>
            <div class="col-sm-6">
              <label for="institucion">Universidad o empresa</label>
              <input type="text" class="form-control" id="institucion" name="institucion" placeholder="USIL" required <?= $cuposAgotados ? 'disabled' : '' ?>>
            </div>
            <div class="col-sm-6">
              <label for="telefono">Teléfono (opcional)</label>
              <input type="tel" class="form-control" id="telefono" name="telefono" placeholder="987 654 321" <?= $cuposAgotados ? 'disabled' : '' ?>>
            </div>
            <div class="col-12">
              <label for="eje_tematico">Eje temático de tu interés</label>
              <select class="form-select" id="eje_tematico" name="eje_tematico" required <?= $cuposAgotados ? 'disabled' : '' ?>>
                <option value="" selected disabled>Elige una opción</option>
                <?php foreach ($ejesTematicos as $eje): ?>
                  <option value="<?= htmlspecialchars($eje['nombre']) ?>"><?= htmlspecialchars($eje['nombre']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-12 mt-2">
              <button type="submit" class="btn btn-naranja w-100" <?= $cuposAgotados ? 'disabled' : '' ?>>
                <?= $cuposAgotados ? 'Cupos agotados' : 'Confirmar mi inscripción' ?>
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>

<!-- ===================== FOOTER ===================== -->
<footer class="footer-congreso">
  <div class="container d-flex flex-wrap justify-content-between align-items-center gap-2">
    <div>© 2026 CITIC — Congreso Internacional de Tecnología e Innovación. Programación de Dispositivos Móviles, USIL.</div>
    <div><a href="admin/login.php">Acceso administrador</a></div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
