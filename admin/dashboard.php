<?php
/**
 * ============================================================
 * Panel de administración — Dashboard de inscritos
 * ============================================================
 */
require_once __DIR__ . '/../config.php';
session_start();

// --- Proteger la ruta: si no hay sesión de admin, fuera de aquí ---
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// --- KPIs ---
$totalInscritos = (int) $conexion->query('SELECT COUNT(*) AS total FROM inscritos')->fetch_assoc()['total'];
$cuposDisponibles = max(0, CUPO_MAXIMO - $totalInscritos);
$porcentajeOcupacion = CUPO_MAXIMO > 0 ? round(($totalInscritos / CUPO_MAXIMO) * 100) : 0;

// --- Inscritos por eje temático (para el gráfico) ---
$porEje = [];
$res = $conexion->query('SELECT eje_tematico, COUNT(*) AS total FROM inscritos GROUP BY eje_tematico ORDER BY total DESC');
while ($fila = $res->fetch_assoc()) {
    $porEje[] = $fila;
}

// --- Listado completo de inscritos (el buscador de la tabla es 100% JS, del lado del cliente) ---
$inscritos = $conexion->query('SELECT nombres, apellidos, email, documento, institucion, telefono, eje_tematico, fecha_inscripcion
                                FROM inscritos ORDER BY fecha_inscripcion DESC')->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard de inscritos · CITIC 2026</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="admin-shell">

<div class="admin-topbar">
  <div class="brand">CITIC <span class="acento">Dashboard</span></div>
  <div class="d-flex align-items-center gap-3">
    <span class="d-none d-sm-inline" style="color:#a9bde3; font-size:0.9rem;">
      <i class="bi bi-person-circle"></i> <?= htmlspecialchars($_SESSION['admin_nombre']) ?>
    </span>
    <a href="logout.php" class="btn btn-sm btn-naranja"><i class="bi bi-box-arrow-right"></i> Salir</a>
  </div>
</div>

<div class="container py-4">

  <!-- ===================== KPIs ===================== -->
  <div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
      <div class="kpi-card">
        <div class="kpi-icon navy"><i class="bi bi-people"></i></div>
        <div class="kpi-value"><?= $totalInscritos ?></div>
        <div class="kpi-label">Total de inscritos</div>
      </div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="kpi-card">
        <div class="kpi-icon orange"><i class="bi bi-ticket-perforated"></i></div>
        <div class="kpi-value"><?= $cuposDisponibles ?></div>
        <div class="kpi-label">Cupos disponibles</div>
      </div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="kpi-card">
        <div class="kpi-icon blue"><i class="bi bi-bar-chart-line"></i></div>
        <div class="kpi-value"><?= $porcentajeOcupacion ?>%</div>
        <div class="kpi-label">Ocupación del congreso</div>
      </div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="kpi-card">
        <div class="kpi-icon navy"><i class="bi bi-diagram-3"></i></div>
        <div class="kpi-value"><?= count($porEje) ?></div>
        <div class="kpi-label">Ejes temáticos con inscritos</div>
      </div>
    </div>
  </div>

  <div class="row g-3 mb-4">
    <!-- ===================== GRÁFICO POR EJE ===================== -->
    <div class="col-lg-4">
      <div class="admin-card h-100">
        <h6 class="mb-3">Inscritos por eje temático</h6>
        <?php if (empty($porEje)): ?>
          <p class="text-muted small mb-0">Todavía no hay inscritos.</p>
        <?php else: ?>
          <canvas id="graficoEjes" height="220"></canvas>
        <?php endif; ?>
      </div>
    </div>

    <!-- ===================== TABLA DE INSCRITOS ===================== -->
    <div class="col-lg-8">
      <div class="admin-card h-100">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
          <h6 class="mb-0">Personas inscritas</h6>
          <input type="search" id="buscador" class="form-control form-control-sm" style="max-width:240px;" placeholder="Buscar por nombre o correo...">
        </div>
        <div class="table-responsive">
          <table class="table table-inscritos align-middle" id="tablaInscritos">
            <thead>
              <tr>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Institución</th>
                <th>Eje temático</th>
                <th>Inscrito el</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($inscritos)): ?>
                <tr><td colspan="5" class="text-center text-muted py-4">Todavía no hay inscritos registrados.</td></tr>
              <?php endif; ?>
              <?php foreach ($inscritos as $p): ?>
                <tr>
                  <td>
                    <div class="fw-semibold"><?= htmlspecialchars($p['nombres'] . ' ' . $p['apellidos']) ?></div>
                    <div class="text-muted small"><?= htmlspecialchars($p['documento']) ?></div>
                  </td>
                  <td><?= htmlspecialchars($p['email']) ?></td>
                  <td><?= htmlspecialchars($p['institucion']) ?></td>
                  <td><span class="badge-eje"><?= htmlspecialchars($p['eje_tematico']) ?></span></td>
                  <td class="text-muted small"><?= date('d/m/Y H:i', strtotime($p['fecha_inscripcion'])) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
  // --- Buscador de la tabla: 100% del lado del cliente, sin recargar la página ---
  document.getElementById('buscador')?.addEventListener('input', function () {
    const texto = this.value.toLowerCase();
    document.querySelectorAll('#tablaInscritos tbody tr').forEach(function (fila) {
      fila.style.display = fila.textContent.toLowerCase().includes(texto) ? '' : 'none';
    });
  });

  // --- Gráfico de inscritos por eje temático ---
  <?php if (!empty($porEje)): ?>
  const ctx = document.getElementById('graficoEjes');
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: <?= json_encode(array_column($porEje, 'eje_tematico')) ?>,
      datasets: [{
        data: <?= json_encode(array_map('intval', array_column($porEje, 'total'))) ?>,
        backgroundColor: '#004eea',
        borderRadius: 6,
        maxBarThickness: 28,
      }]
    },
    options: {
      indexAxis: 'y',
      plugins: { legend: { display: false } },
      scales: {
        x: { ticks: { stepSize: 1, precision: 0 }, grid: { color: '#e4eaf7' } },
        y: { grid: { display: false } }
      }
    }
  });
  <?php endif; ?>
</script>
</body>
</html>
