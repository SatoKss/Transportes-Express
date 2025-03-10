
let map;

document.addEventListener("DOMContentLoaded", function () {
    console.log("✅ Script cargado correctamente.");

    $("#valor").on("input", function () {
        let valorInput = $(this).val();

        if (!/^\d*$/.test(valorInput)) {
            $("#valorError").show();
            $(this).val(valorInput.replace(/\D/g, ""));
        } else {
            $("#valorError").hide();
        }
    });

    window.filterTable = function () {
        let searchEjecutivo = $("#searchEjecutivo").val().toLowerCase();
        let startDate = $("#startDate").val();
        let endDate = $("#endDate").val();

        $("table tbody tr").each(function () {
            let row = $(this);
            let ejecutivo = row.find("td:eq(4)").text().toLowerCase();
            let fechaViaje = row.find("td:eq(1)").text();

            let matchEjecutivo = searchEjecutivo === "" || ejecutivo.includes(searchEjecutivo);
            let matchFecha = (startDate === "" || fechaViaje >= startDate) && (endDate === "" || fechaViaje <= endDate);

            if (matchEjecutivo && matchFecha) {
                row.show();
            } else {
                row.hide();
            }
        });
    };

    window.resetFilters = function () {
        $("#searchEjecutivo").val("");
        $("#startDate").val("");
        $("#endDate").val("");
        $("table tbody tr").show();
    };

    $("#detalleModal").on("show.bs.modal", function (event) {
        let button = $(event.relatedTarget);
        let valor = button.data("bs-valor");

        let formattedValor = new Intl.NumberFormat("es-CL").format(valor);
        $("#detalleValor").text(formattedValor + " CLP");
    });

    $("table tbody tr").each(function () {
        let row = $(this);
        let valor = parseInt(row.find("td:eq(6)").text().replace(/\./g, ""));

        if (valor < 20000) {
            row.find("td:eq(6)").css("color", "red");
        }
    });
});

document.addEventListener("DOMContentLoaded", function () {
    $("#viajeForm").off("submit").on("submit", function (e) {
        e.preventDefault();

        let fecha = $("#fecha_viaje").val().trim();
        let origen = $("#origen_comuna").val().trim();
        let destino = $("#destino_comuna").val().trim();
        let ejecutivo = $("#usuario_ejecutivo").val().trim();
        let solicitante = $("#usuario_solicitante").val().trim();
        let valor = $("#valor").val().trim();

        // Ocultar errores previos
        $("#origenError").hide().text("");
        $("#destinoError").hide().text("");

        console.log("Validando formulario...");

        if (!fecha || !origen || !destino || !ejecutivo || !solicitante || !valor) {
            alert("⚠️ Todos los campos son obligatorios.");
            return;
        }

        if (!/^\d+$/.test(valor)) {
            alert("⚠️ El valor (CLP) debe ser un número sin puntos ni caracteres especiales.");
            return;
        }

        // Validar direcciones antes de enviar el formulario
        validarDireccion(origen, function (origenValido) {
            if (!origenValido) {
                console.warn("❌ Dirección de origen inválida:", origen);
                $("#origenError").text("⚠️ La dirección de origen no es válida. Ingrese una dirección real.").show();
                return;
            }

            validarDireccion(destino, function (destinoValido) {
                if (!destinoValido) {
                    console.warn("❌ Dirección de destino inválida:", destino);
                    $("#destinoError").text("⚠️ La dirección de destino no es válida. Ingrese una dirección real.").show();
                    return;
                }

                console.log("✅ Direcciones válidas. Enviando formulario...");
                enviarFormulario();
            });
        });
    });

    function validarDireccion(direccion, callback) {
        let geocoder = new google.maps.Geocoder();

        geocoder.geocode({ address: direccion + ", Chile" }, function (results, status) {
            if (status === "OK" && results.length > 0) {
                let result = results[0];
                let formattedAddress = result.formatted_address.toLowerCase();
                let inputAddress = direccion.toLowerCase();

                let validTypes = ["locality", "route", "sublocality", "street_address"];
                let isValidType = result.types.some(type => validTypes.includes(type));

                console.log("🔍 Verificando dirección:", inputAddress, "=>", formattedAddress);

                if (!isValidType || !formattedAddress.includes(inputAddress)) {
                    console.warn("❌ Dirección no coincide:", formattedAddress);
                    callback(false);
                } else {
                    console.log("✅ Dirección válida:", formattedAddress);
                    callback(true);
                }
            } else {
                console.warn(`❌ Dirección no válida: ${direccion}`);
                callback(false);
            }
        });
    }

    function enviarFormulario() {
        let id = $("#viajeId").val();
        let url = id ? "../src/update_viaje.php" : "../src/add_viaje.php";
        let data = $("#viajeForm").serialize();

        console.log("📤 Enviando datos al servidor...", data);

        $.post(url, data, function (response) {
            console.log("✔ Respuesta del servidor:", response);

            let modalElement = document.getElementById("viajeModal");
            let modalInstance = bootstrap.Modal.getInstance(modalElement);
            if (modalInstance) {
                modalInstance.hide();
            }

            let successModalEl = document.getElementById("successModal");
            let successModalTitle = document.getElementById("successModalTitle");
            let successModalMsg = document.getElementById("successModalMsg");
            let successModal = new bootstrap.Modal(successModalEl);

            successModalTitle.textContent = id ? "Viaje Editado" : "Viaje Agregado";
            successModalMsg.textContent = id ? "El viaje se ha editado con éxito." : "El viaje se ha agregado con éxito.";

            successModal.show();

            actualizarTabla();
        }).fail(function (xhr) {
            console.error("❌ Error en la petición AJAX:", xhr.responseText);
        });
    }
});







const viajeModal = document.getElementById("viajeModal");
viajeModal.addEventListener("show.bs.modal", event => {
    const button = event.relatedTarget;
    if (!button) return;

    const id = button.getAttribute("data-bs-id");
    const fecha = button.getAttribute("data-bs-fecha");
    const origen = button.getAttribute("data-bs-origen");
    const destino = button.getAttribute("data-bs-destino");
    const ejecutivo = button.getAttribute("data-bs-ejecutivo");
    const solicitante = button.getAttribute("data-bs-solicitante");
    const valor = button.getAttribute("data-bs-valor");

    document.getElementById("modalTitle").textContent = id ? "Editar Viaje" : "Agregar Viaje";

    document.getElementById("viajeId").value = id || "";
    document.getElementById("fecha_viaje").value = fecha || "";
    document.getElementById("origen_comuna").value = origen || "";
    document.getElementById("destino_comuna").value = destino || "";
    document.getElementById("usuario_ejecutivo").value = ejecutivo || "";
    document.getElementById("usuario_solicitante").value = solicitante || "";
    document.getElementById("valor").value = valor || "";
});

const detalleModal = document.getElementById("detalleModal");
detalleModal.addEventListener("show.bs.modal", event => {
    const button = event.relatedTarget;
    if (!button) return;

    const fecha = button.getAttribute("data-bs-fecha");
    const origen = button.getAttribute("data-bs-origen");
    const destino = button.getAttribute("data-bs-destino");
    const ejecutivo = button.getAttribute("data-bs-ejecutivo");
    const solicitante = button.getAttribute("data-bs-solicitante");
    const valor = button.getAttribute("data-bs-valor");

    document.getElementById("detalleFecha").textContent = fecha;
    document.getElementById("detalleOrigen").textContent = origen;
    document.getElementById("detalleDestino").textContent = destino;
    document.getElementById("detalleEjecutivo").textContent = ejecutivo;
    document.getElementById("detalleSolicitante").textContent = solicitante;
    document.getElementById("detalleValor").textContent = valor;

    verMapaDetalle(origen, destino);
});

$('#detalleModal').on('shown.bs.modal', function () {
    console.log("🔄 Ajustando tamaño del mapa...");

    $("#mapDetalle").css("height", "60vh");
    google.maps.event.trigger(map, "resize");
});




const confirmDeleteModal = document.getElementById("confirmDeleteModal");
confirmDeleteModal.addEventListener("show.bs.modal", event => {
    const button = event.relatedTarget;
    if (button) {
        document.getElementById("deleteViajeId").value = button.getAttribute("data-bs-id");
    }
});


function confirmarEliminar() {
    let id = document.getElementById("deleteViajeId").value;
    console.log("🗑 Eliminando viaje con ID:", id);

    $.post("../src/delete_viaje.php", { id: id }, function (response) {
        console.log("✔ Viaje eliminado:", response);

        let confirmDeleteEl = document.getElementById("confirmDeleteModal");
        let confirmDeleteInstance = bootstrap.Modal.getInstance(confirmDeleteEl);
        if (confirmDeleteInstance) {
            confirmDeleteInstance.hide();
        }

        let successModalEl = document.getElementById("successModal");
        let successModalTitle = document.getElementById("successModalTitle");
        let successModalMsg = document.getElementById("successModalMsg");
        let successModal = new bootstrap.Modal(successModalEl);

        successModalTitle.textContent = "Viaje Eliminado";
        successModalMsg.textContent = "El viaje se ha eliminado con éxito.";
        successModal.show();

        actualizarTabla();
    }).fail(function (xhr) {
        console.error("❌ Error al eliminar el viaje:", xhr.responseText);
    });
}






function verMapaDetalle(origen, destino) {
    let mapElement = document.getElementById("mapDetalle");

    if (!mapElement) {
        console.error("❌ Error: El elemento #mapDetalle no existe.");
        return;
    }

    console.log("🗺️ Creando mapa en #mapDetalle...");

    mapElement.style.height = "400px";

    let map = new google.maps.Map(mapElement, {
        zoom: 12,
        center: { lat: -33.4489, lng: -70.6693 }, // Santiago, Chile
    });

    let directionsService = new google.maps.DirectionsService();
    let directionsRenderer = new google.maps.DirectionsRenderer();
    directionsRenderer.setMap(map);

    directionsService.route(
        {
            origin: origen,
            destination: destino,
            travelMode: google.maps.TravelMode.DRIVING,
        },
        function (response, status) {
            if (status === "OK") {
                directionsRenderer.setDirections(response);
                let distanciaKm = response.routes[0].legs[0].distance.text;
                let tiempoEstimado = response.routes[0].legs[0].duration.text;
                document.getElementById("distancia").innerText = distanciaKm;
                document.getElementById("tiempo").innerText = tiempoEstimado;
            } else {
                alert("No se pudo calcular la ruta: " + status);
            }
        }
    );
}







function reloadPage() {
    location.reload();
}


function formatCurrency(value) {
    return new Intl.NumberFormat('es-CL').format(value);
}

document.addEventListener("DOMContentLoaded", function () {
    var detalleModal = document.getElementById("detalleModal");

    detalleModal.addEventListener("show.bs.modal", function (event) {
        var button = event.relatedTarget; // Botón que activó el modal
        var valor = button.getAttribute("data-bs-valor");

        document.getElementById("detalleValor").innerText = formatCurrency(valor) + " CLP";
    });
});

function filterTable() {
    let ejecutivoText = document.getElementById("searchEjecutivo").value.toLowerCase();
    let startDate = document.getElementById("startDate").value;
    let endDate = document.getElementById("endDate").value;

    let table = document.querySelector("table tbody");
    let rows = table.getElementsByTagName("tr");

    for (let row of rows) {
        let ejecutivo = row.cells[4].textContent.toLowerCase();
        let fecha = row.cells[1].textContent;

        let showRow = true;

        if (ejecutivoText && !ejecutivo.includes(ejecutivoText)) {
            showRow = false;
        }

        if (startDate && endDate) {
            if (fecha < startDate || fecha > endDate) {
                showRow = false;
            }
        }

        row.style.display = showRow ? "" : "none";
    }
}

function resetFilters() {
    document.getElementById("searchEjecutivo").value = "";
    document.getElementById("startDate").value = "";
    document.getElementById("endDate").value = "";

    let rows = document.querySelectorAll("tbody tr");
    rows.forEach(row => {
        row.style.display = "";
    });
}

function cargarReporte(tipo) {
    fetch(`reportes.php?reporte=${tipo}`)
        .then(response => response.json())
        .then(data => {
            console.log("Reporte recibido:", data);

            if (data.length === 0) {
                alert("No hay datos para este reporte.");
                return;
            }

            const headerRow = document.getElementById("reporte-header");
            const bodyTable = document.getElementById("reporte-body");

            headerRow.innerHTML = "";
            bodyTable.innerHTML = "";

            const columnas = Object.keys(data[0]);

            columnas.forEach(col => {
                const th = document.createElement("th");
                th.textContent = col.toUpperCase().replace("_", " ");
                headerRow.appendChild(th);
            });

            data.forEach(row => {
                const tr = document.createElement("tr");
                columnas.forEach(col => {
                    const td = document.createElement("td");
                    td.textContent = row[col];
                    tr.appendChild(td);
                });
                bodyTable.appendChild(tr);
            });

            document.getElementById("reporte-container").style.display = "block";
        })
        .catch(error => {
            console.error("Error al cargar el reporte:", error);
            alert("Hubo un problema al cargar el reporte.");
        });
}


function abrirModal(reporte) {
    fetch(`reportes.php?reporte=${reporte}`)
        .then(response => response.json())
        .then(data => {
            let modalBody = document.getElementById("modalBody");
            modalBody.innerHTML = generarTablaHTML(data);
            let myModal = new bootstrap.Modal(document.getElementById("reportesModal"));
            myModal.show();
        })
        .catch(error => console.error("Error al cargar el reporte:", error));
}

function generarTablaHTML(data) {
    if (data.length === 0) return "<p>No hay datos disponibles.</p>";

    let table = `<table class="table table-striped">
                    <thead>
                        <tr>${Object.keys(data[0]).map(key => `<th>${key}</th>`).join("")}</tr>
                    </thead>
                    <tbody>
                        ${data.map(row => `<tr>${Object.values(row).map(value => `<td>${value}</td>`).join("")}</tr>`).join("")}
                    </tbody>
                </table>`;
    return table;
}

function exportarReporte(tipo) {
    $.ajax({
        url: `../src/exportar_reportes_google_sheets.php?reporte=${tipo}`,
        type: 'GET',
        success: function (response) {
            console.log("✅ Respuesta del servidor:", response);

            let successModalEl = document.getElementById("successModal");
            let successModalTitle = document.getElementById("successModalTitle");
            let successModalMsg = document.getElementById("successModalMsg");
            let successModal = new bootstrap.Modal(successModalEl);

            if (response.status === "success") {
                successModalTitle.textContent = "Exportación Exitosa";
                successModalMsg.textContent = response.message;
            } else {
                successModalTitle.textContent = "Error en la Exportación";
                successModalMsg.textContent = response.message;
            }

            successModal.show();
        },
        error: function (xhr) {
            console.error("❌ Error en la exportación:", xhr.responseText);
            alert("⚠️ Error al exportar los datos.");
        }
    });
}


function actualizarTabla() {
    $.get("../src/get_viajes.php", function (data) {
        let viajes = JSON.parse(data);
        let tbody = $("table tbody");
        tbody.empty();

        viajes.forEach((viaje) => {
            let row = `
                <tr>
                    <td>${viaje.id}</td>
                    <td>${viaje.fecha_viaje}</td>
                    <td>${viaje.origen_comuna}</td>
                    <td>${viaje.destino_comuna}</td>
                    <td>${viaje.usuario_ejecutivo}</td>
                    <td>${viaje.usuario_solicitante}</td>
                    <td class="${viaje.valor < 20000 ? 'text-danger fw-bold' : ''}">${new Intl.NumberFormat('es-CL').format(viaje.valor)}</td>
                    <td>
                        <button class="btn btn-warning btn-sm" 
                                data-bs-toggle="modal" 
                                data-bs-target="#viajeModal"
                                data-bs-id="${viaje.id}"
                                data-bs-fecha="${viaje.fecha_viaje}"
                                data-bs-origen="${viaje.origen_comuna}"
                                data-bs-destino="${viaje.destino_comuna}"
                                data-bs-ejecutivo="${viaje.usuario_ejecutivo}"
                                data-bs-solicitante="${viaje.usuario_solicitante}"
                                data-bs-valor="${viaje.valor}">
                            ✏️ Editar
                        </button>
                        <button class="btn btn-danger btn-sm" 
                                data-bs-toggle="modal" 
                                data-bs-target="#confirmDeleteModal" 
                                data-bs-id="${viaje.id}">
                            🗑 Eliminar
                        </button>
                        <button class="btn btn-info btn-sm" 
                                data-bs-toggle="modal" 
                                data-bs-target="#detalleModal"
                                data-bs-fecha="${viaje.fecha_viaje}"
                                data-bs-origen="${viaje.origen_comuna}"
                                data-bs-destino="${viaje.destino_comuna}"
                                data-bs-ejecutivo="${viaje.usuario_ejecutivo}"
                                data-bs-solicitante="${viaje.usuario_solicitante}"
                                data-bs-valor="${viaje.valor}">
                            📄 Ver Detalle
                        </button>
                    </td>
                </tr>`;
            tbody.append(row);
        });
    });
}

function initMap() {
    console.log("✅ Google Maps API cargada correctamente.");
}




