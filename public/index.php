<?php
include '../src/db.php';

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Consulta SQL
$sql = "SELECT * FROM viajes ORDER BY fecha_viaje DESC";
$result = $conn->query($sql);

// Verificar si la consulta falló
if (!$result) {
    die("Error en la consulta SQL: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Panel de Administración</title>

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

  <!-- jQuery y Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Google Maps API -->
<script src="https://maps.googleapis.com/maps/api/js?key=&libraries=places"></script>


  <!-- Tus estilos y script -->
  <link rel="stylesheet" href="styles.css">
  <script src="script.js" defer></script>
</head>
<body class="bg-dark text-light">

  <!-- Navbar con menú desplegable -->
<nav class="navbar navbar-expand-lg navbar-dark bg-black shadow">
    <div class="container-fluid">
    <a class="navbar-brand fw-bold d-flex align-items-center" href="#">
    <i class="fa-solid fa-user-tie me-2" style="color: white; font-size: 30px;"></i>
    Panel de Administrador
</a>



        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="dropdown">
    <button class="btn btn-warning dropdown-toggle" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        📄 Exportar a Google Sheets
    </button>
    <ul class="dropdown-menu" aria-labelledby="exportDropdown">
        <li><a class="dropdown-item" href="#" onclick="exportarReporte('enero')">📅 Exportar Reporte de Enero</a></li>
        <li><a class="dropdown-item" href="#" onclick="exportarReporte('ejecutivos')">👤 Exportar Reporte por Ejecutivo</a></li>
        <li><a class="dropdown-item" href="#" onclick="exportarReporte('bajos')">🔻 Exportar Viajes con Valor Bajo</a></li>
        <li><a class="dropdown-item" href="#" onclick="exportarReporte('rango_fechas')">📍 Exportar Filtrado por Fechas y Comunas</a></li>
    </ul>
</div>
    </div>
</nav>


  

  <div class="container mt-4">
  <h2 class="listado-viajes text-center mb-4">Listado de Viajes</h2>

    <div class="d-flex justify-content-between align-items-center mb-3">

  </div>


    <!-- Filtros de búsqueda -->
<div class="row mb-3">
    <div class="col-md-4">
        <input type="text" id="searchEjecutivo" class="form-control" placeholder="🔍 Buscar por ejecutivo">
    </div>
    <div class="col-md-3">
        <input type="date" id="startDate" class="form-control" placeholder="Fecha inicio">
    </div>
    <div class="col-md-3">
        <input type="date" id="endDate" class="form-control" placeholder="Fecha fin">
    </div>
    <div class="col-md-2 d-flex">
        <button class="btn btn-primary me-2" onclick="filterTable()">Filtrar</button>
        <button class="btn btn-secondary" onclick="resetFilters()">
            🔄
        </button>
    </div>
</div>



        <div class="table-responsive">
            <table class="table table-striped table-hover text-light">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Origen</th>
                        <th>Destino</th>
                        <th>Ejecutivo</th>
                        <th>Solicitante</th>
                        <th>Valor (CLP)</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= $row['fecha_viaje'] ?></td>
                            <td><?= $row['origen_comuna'] ?></td>
                            <td><?= $row['destino_comuna'] ?></td>
                            <td><?= $row['usuario_ejecutivo'] ?></td>
                            <td><?= $row['usuario_solicitante'] ?></td>
                            <td class="<?= ($row['valor'] < 20000) ? 'text-danger fw-bold' : '' ?>">
                            <?= number_format($row['valor'], 0, ',', '.') ?>
                        </td>                            
                        <td>
                                <!-- Botón Editar -->
                                <button class="btn btn-warning btn-sm" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#viajeModal"
                                        data-bs-id="<?= $row['id'] ?>"
                                        data-bs-fecha="<?= $row['fecha_viaje'] ?>"
                                        data-bs-origen="<?= $row['origen_comuna'] ?>"
                                        data-bs-destino="<?= $row['destino_comuna'] ?>"
                                        data-bs-ejecutivo="<?= $row['usuario_ejecutivo'] ?>"
                                        data-bs-solicitante="<?= $row['usuario_solicitante'] ?>"
                                        data-bs-valor="<?= $row['valor'] ?>">
                                    ✏️ Editar
                                </button>

                                <!-- Botón Eliminar -->
                                <button class="btn btn-danger btn-sm" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#confirmDeleteModal" 
                                        data-bs-id="<?= $row['id'] ?>">
                                    🗑 Eliminar
                                </button>

                                <!-- Botón Ver Detalle -->
                                <button class="btn btn-info btn-sm" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#detalleModal"
                                        data-bs-fecha="<?= $row['fecha_viaje'] ?>"
                                        data-bs-origen="<?= $row['origen_comuna'] ?>"
                                        data-bs-destino="<?= $row['destino_comuna'] ?>"
                                        data-bs-ejecutivo="<?= $row['usuario_ejecutivo'] ?>"
                                        data-bs-solicitante="<?= $row['usuario_solicitante'] ?>"
                                        data-bs-valor="<?= $row['valor'] ?>">
                                    📄 Ver Detalle
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>



<!-- Tabla donde se mostrarán los reportes -->
<div class="table-responsive">
    <table class="table table-bordered table-hover text-light" id="reporteTabla" style="display: none;">
        <thead class="table-dark" id="reporteHead">
            <!-- Aquí se insertarán las columnas dinámicamente -->
        </thead>
        <tbody id="reporteBody">
            <!-- Aquí se insertarán los datos dinámicamente -->
        </tbody>
    </table>
</div>


    

    

    <!-- ===================================== -->
    <!-- AQUI INCLUIMOS LOS MODALES DIRECTAMENTE -->
    <!-- ===================================== -->

    <!-- Modal de Agregar/Editar Viaje -->
  <div class="modal fade" id="viajeModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content bg-light text-dark">
        <div class="modal-header">
          <h5 class="modal-title" id="modalTitle">Agregar Viaje</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <form id="viajeForm">
            <input type="hidden" id="viajeId" name="id">
            <div class="mb-3">
              <label for="fecha_viaje" class="form-label">Fecha</label>
              <input type="date" class="form-control" id="fecha_viaje" name="fecha_viaje" required>
            </div>
            <div class="mb-3">
              <label for="origen_comuna" class="form-label">Origen</label>
              <input type="text" class="form-control" id="origen_comuna" name="origen_comuna" required>
            </div>
            <div class="mb-3">
              <label for="destino_comuna" class="form-label">Destino</label>
              <input type="text" class="form-control" id="destino_comuna" name="destino_comuna" required>
            </div>
            <div class="mb-3">
              <label for="usuario_ejecutivo" class="form-label">Ejecutivo</label>
              <input type="text" class="form-control" id="usuario_ejecutivo" name="usuario_ejecutivo" required>
            </div>
            <div class="mb-3">
              <label for="usuario_solicitante" class="form-label">Solicitante</label>
              <input type="text" class="form-control" id="usuario_solicitante" name="usuario_solicitante" required>
            </div>
            <div class="mb-3">
              <label for="valor" class="form-label">Valor (CLP)</label>
              <input type="number" class="form-control" id="valor" name="valor" required>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-success">Guardar</button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

<!-- Modal de Ver Detalles -->
<div class="modal fade" id="detalleModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog" style="max-width: 800px;"> <!-- Ajustamos el ancho del modal -->
    <div class="modal-content">
      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title">Detalles del Viaje</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
          <div class="row"> <!-- Eliminamos la alineación vertical -->
            <div class="col-md-6"> <!-- Ajustamos la columna de información -->
              <h6><strong>Fecha:</strong> <span id="detalleFecha"></span></h6>
              <h6><strong>Origen:</strong> <span id="detalleOrigen"></span></h6>
              <h6><strong>Destino:</strong> <span id="detalleDestino"></span></h6>
              <h6><strong>Ejecutivo:</strong> <span id="detalleEjecutivo"></span></h6>
              <h6><strong>Solicitante:</strong> <span id="detalleSolicitante"></span></h6>
              <h6 class="text-success"><strong>Valor (CLP):</strong> <span id="detalleValor"></span></h6>
              <h6><strong>Distancia:</strong> <span id="distancia">Cargando...</span></h6>
              <h6><strong>Tiempo estimado:</strong> <span id="tiempo">Cargando...</span></h6>
            </div>
            <div class="col-md-6"> <!-- Ajustamos la columna del mapa -->
              <div id="mapDetalle" class="rounded shadow" style="width: 100%; height: 100px; border: 1px solid #ccc;"></div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer bg-dark">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>


  <!-- Modal de Confirmación de Eliminación -->
  <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content bg-light text-dark">
        <div class="modal-header">
          <h5 class="modal-title">Confirmar Eliminación</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <p>¿Estás seguro de que deseas eliminar este viaje?</p>
          <input type="hidden" id="deleteViajeId">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" onclick="confirmarEliminar()">Sí, eliminar</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </div>
  </div>

 <!-- Modal de Éxito -->
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content bg-light text-dark">
            <div class="modal-header">
                <h5 class="modal-title" id="successModalTitle">Mensaje</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p id="successModalMsg"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Aceptar</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="reportesModal" tabindex="-1" aria-labelledby="reportesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reportesModalLabel">Reporte</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body" id="modalBody">
                Cargando...
            </div>
        </div>
    </div>
</div>


  
<!-- Botón flotante para Agregar Viaje -->
<button class="btn btn-success floating-button" data-bs-toggle="modal" data-bs-target="#viajeModal">
    ➕ Agregar Viaje
</button>




</body>
</html>