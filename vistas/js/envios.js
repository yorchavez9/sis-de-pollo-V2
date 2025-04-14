class EnviosManager {
    constructor() {
      this.contadorPaquetes = 0;
      this.contadorItems = 0;
      this.envioActual = null;
      this.init();
    }
  
    async init() {
      this.setupSelect2();
      this.setupEventListeners();
      await this.loadInitialData();
      this.fechaHoraActual();
    }
  
    setupSelect2() {
      this.select2Config = {
        placeholder: "Seleccionar",
        width: '100%',
        language: 'es'
      };
  
      $('.select').select2(this.select2Config);
      
      $('#modalNuevoEnvio, #modalDetalleEnvio, #modalCambiarEstado, #modalSubirDocumento').on('shown.bs.modal', () => {
        $('.select').select2(this.select2Config);
        this.fechaHoraActual();
      });
    }
  
    setupEventListeners() {
      // Formularios
      $('#formNuevoEnvio').on('submit', (e) => this.handleCrearEnvio(e));
      $('#formCambiarEstado').on('submit', (e) => this.handleCambiarEstado(e));
      $('#formSubirDocumento').on('submit', (e) => this.handleSubirDocumento(e));
      
      // Botones
      $('#btnNuevoEnvio').click(() => this.prepareNuevoEnvio());
      $('#btnAgregarPaquete').click(() => this.agregarPaquete());
      $('#btnCalcularCosto').click(() => this.calcularCostoEnvio());
      $('#btnFiltrarEnvios').click((e) => this.filtrarEnvios(e));
      $('#btnSubirDocumento').click(() => this.prepareSubirDocumento());
      $('#btnImprimirGuia').click(() => this.imprimirGuia());
      $('#btnNuevoSeguimiento').click(() => this.prepareCambiarEstado());
      
      // Eventos delegados
      $('#tablaEnvios').on('click', '.btnDetalleEnvio', (e) => this.mostrarDetalleEnvio($(e.currentTarget).data('id')));
      $('#tablaEnvios').on('click', '.btnCambiarEstado', (e) => this.prepareCambiarEstado($(e.currentTarget).data('id')));
      $('#tablaEnvios').on('click', '.btnCancelarEnvio', (e) => this.cancelarEnvio($(e.currentTarget).data('id')));
      $('#contenedorPaquetes').on('click', '.btnEliminarPaquete', (e) => this.eliminarPaquete(e));
      $('#contenedorPaquetes').on('click', '.btnAgregarItem', (e) => this.agregarItem(e));
      $('#contenedorPaquetes').on('change', '.selectProducto', (e) => this.actualizarPesoProducto(e));
      $('#contenedorPaquetes').on('input', '.peso, .alto, .ancho, .profundidad', () => this.actualizarResumen());
    }
  
    async loadInitialData() {
      try {
        await Promise.all([
          this.cargarSucursales("filtroOrigen", true),
          this.cargarSucursales("filtroDestino", true),
          this.cargarSerieComprobante("id_serie"),
          this.cargarTipoEncomienda("id_tipo_encomienda"),
          this.cargarSucursales("id_sucursal_origen"),
          this.cargarSucursales("id_sucursal_destino"),
          this.cargarTransportistas("id_transportista"),
          this.mostrarEnvios()
        ]);
      } catch (error) {
        this.showError("Error al cargar datos iniciales", error);
      }
    }
  
    // ================ MÉTODOS PRINCIPALES ================
  
    async mostrarEnvios(filtros = {}) {
        try {
            // Crear FormData y agregar los parámetros
            const formData = new FormData();
            formData.append('action', 'listar');
            
            // Agregar cada filtro al FormData
            Object.keys(filtros).forEach(key => {
                if (filtros[key] !== undefined && filtros[key] !== null) {
                    formData.append(key, filtros[key]);
                }
            });
    
            // Enviar como POST con FormData
            const response = await this.fetchData("ajax/envios.ajax.php", "POST", formData);
            console.log(response);
    
            if (!response) {
                throw new Error("No se recibió respuesta del servidor");
            }
    
            if (!response.status) {
                throw new Error(response.message || "Error desconocido del servidor");
            }
    
            this.renderTablaEnvios(response.data);
        } catch (error) {
            console.error("Detalles del error:", {
                error: error.message,
                stack: error.stack,
                filtros
            });
            this.showError("Error al cargar envíos", error);
        }
    }
  
    async mostrarDetalleEnvio(idEnvio) {
      try {
        const response = await this.fetchData("ajax/envios.ajax.php", "GET", { action: 'detalle', id: idEnvio });
        
        if (!response.status) throw new Error(response.message);
        
        this.envioActual = response.data.envio;
        this.renderDetalleEnvio(response.data);
        $('#modalDetalleEnvio').modal('show');
      } catch (error) {
        this.showError("Error al cargar detalle", error);
      }
    }
  
    async actualizarEstadoEnvio(idEnvio, estado, observaciones = null) {
      try {
        const formData = new FormData();
        formData.append("id_envio", idEnvio);
        formData.append("estado", estado);
        formData.append("observaciones", observaciones);
        formData.append("action", "cambiarEstado");
        
        const response = await this.fetchData("ajax/envios.ajax.php", "POST", formData);
        
        if (response.status) {
          this.showSuccess("Estado del envío actualizado");
          await this.mostrarEnvios();
          
          if (this.envioActual && this.envioActual.id_envio == idEnvio) {
            await this.mostrarDetalleEnvio(idEnvio);
          }
          
          $('#modalCambiarEstado').modal('hide');
        } else {
          throw new Error(response.message);
        }
      } catch (error) {
        this.showError("Error al actualizar estado", error);
      }
    }
  
    // ================ MÉTODOS DE FORMULARIOS ================
  
    prepareNuevoEnvio() {
      this.resetForm();
      $("#formNuevoEnvio [name='codigo_envio']").val(this.generarCodigoEnvio());
      $('#modalNuevoEnvio').modal('show');
    }
  
    async handleCrearEnvio(e) {
      e.preventDefault();
      
      try {
        if (!this.validateEnvioForm()) return;
        
        const formData = new FormData(e.target);
        formData.append("action", "crear");
        formData.append("paquetes", JSON.stringify(this.getPaquetesData()));
        
        const response = await this.fetchData("ajax/envios.ajax.php", "POST", formData);
        
        if (response.status) {
          this.showSuccess("Envío creado con éxito");
          this.resetForm();
          $('#modalNuevoEnvio').modal('hide');
          await this.mostrarEnvios();
        } else {
          throw new Error(response.message);
        }
      } catch (error) {
        this.showError("Error al crear envío", error);
      }
    }
  
    prepareCambiarEstado(idEnvio = null) {
      const envioId = idEnvio || (this.envioActual && this.envioActual.id_envio);
      if (!envioId) return;
      
      $("#idEnvioEstado").val(envioId);
      $('#modalCambiarEstado').modal('show');
    }
  
    async handleCambiarEstado(e) {
      e.preventDefault();
      
      try {
        const idEnvio = $("#idEnvioEstado").val();
        const estado = $("#nuevoEstado").val();
        const observaciones = $("#observacionesEstado").val();
        
        if (!idEnvio || !estado) {
          throw new Error("Datos incompletos");
        }
        
        await this.actualizarEstadoEnvio(idEnvio, estado, observaciones);
      } catch (error) {
        this.showError("Error al cambiar estado", error);
      }
    }
  
    prepareSubirDocumento() {
      if (!this.envioActual) return;
      $("#idEnvioDocumento").val(this.envioActual.id_envio);
      $('#modalSubirDocumento').modal('show');
    }
  
    async handleSubirDocumento(e) {
      e.preventDefault();
      
      try {
        if (!this.envioActual) return;
        
        const formData = new FormData(e.target);
        formData.append("id_envio", this.envioActual.id_envio);
        formData.append("action", "subirDocumento");
        
        const response = await this.fetchData("ajax/envios.ajax.php", "POST", formData);
        
        if (response.status) {
          this.showSuccess("Documento subido con éxito");
          $('#modalSubirDocumento').modal('hide');
          e.target.reset();
          await this.mostrarDetalleEnvio(this.envioActual.id_envio);
        } else {
          throw new Error(response.message);
        }
      } catch (error) {
        this.showError("Error al subir documento", error);
      }
    }
  
    // ================ MÉTODOS DE PAQUETES ================
  
    agregarPaquete() {
      this.contadorPaquetes++;
      const template = $("#templatePaquete").html();
      const $paquete = $(template.replace(/{{numero}}/g, this.contadorPaquetes));
      
      $("#contenedorPaquetes").append($paquete);
      this.cargarProductos($paquete.find(".selectProducto"));
      this.actualizarResumen();
    }
  
    eliminarPaquete(e) {
      $(e.currentTarget).closest(".paquete").remove();
      this.contadorPaquetes--;
      this.actualizarResumen();
    }
  
    agregarItem(e) {
      const tbody = $(e.currentTarget).closest(".paquete").find(".table-items tbody");
      this.contadorItems++;
      
      const template = $("#templateItemPaquete").html();
      const $item = $(template);
      
      tbody.append($item);
      this.cargarProductos($item.find(".selectProducto"));
    }
  
    actualizarPesoProducto(e) {
      const peso = $(e.currentTarget).find("option:selected").data("peso") || 0;
      $(e.currentTarget).closest(".item").find(".pesoUnitario").val(peso);
    }
  
    getPaquetesData() {
      const paquetes = [];
      
      $(".paquete").each((index, element) => {
        const $paquete = $(element);
        
        paquetes.push({
          numero: index + 1,
          descripcion: $paquete.find(".descripcion").val(),
          peso: $paquete.find(".peso").val(),
          alto: $paquete.find(".alto").val(),
          ancho: $paquete.find(".ancho").val(),
          profundidad: $paquete.find(".profundidad").val(),
          instrucciones: $paquete.find(".instrucciones").val(),
          items: this.getItemsPaquete($paquete)
        });
      });
      
      return paquetes;
    }
  
    getItemsPaquete($paquete) {
      const items = [];
      
      $paquete.find(".item").each((index, element) => {
        const $item = $(element);
        const $selectProducto = $item.find(".selectProducto");
        
        items.push({
          id_producto: $selectProducto.val(),
          descripcion: $selectProducto.find("option:selected").text(),
          cantidad: $item.find(".cantidad").val(),
          peso_unitario: $item.find(".pesoUnitario").val(),
          valor_unitario: $item.find(".valorUnitario").val() || 0
        });
      });
      
      return items;
    }
  
    actualizarResumen() {
      let totalPaquetes = 0;
      let pesoTotal = 0;
      let volumenTotal = 0;
      
      $(".paquete").each((index, element) => {
        const $paquete = $(element);
        totalPaquetes++;
        
        const peso = parseFloat($paquete.find(".peso").val()) || 0;
        const alto = parseFloat($paquete.find(".alto").val()) || 0;
        const ancho = parseFloat($paquete.find(".ancho").val()) || 0;
        const profundidad = parseFloat($paquete.find(".profundidad").val()) || 0;
        
        pesoTotal += peso;
        volumenTotal += (alto * ancho * profundidad) / 1000000;
      });
      
      $("#totalPaquetes").val(totalPaquetes);
      $("#pesoTotal").val(pesoTotal.toFixed(2));
      $("#volumenTotal").val(volumenTotal.toFixed(2));
    }
  
    // ================ MÉTODOS DE CARGA DE DATOS ================
  
    async cargarSucursales(selectId, todas = false) {
      try {
        const response = await this.fetchData("ajax/sucursal.ajax.php");
        
        if (!response.status) throw new Error(response.message);
        
        const $select = $(`#${selectId}`).empty();
        $select.append(todas ? 
          '<option value="">Todas</option>' : 
          '<option value="" disabled selected>Seleccionar sucursal</option>');
        
        response.data.forEach(sucursal => {
          if (sucursal.estado === 1) {
            $select.append(`<option value="${sucursal.id_sucursal}">${sucursal.nombre}</option>`);
          }
        });
      } catch (error) {
        this.showError("Error al cargar sucursales", error);
      }
    }
  
    async cargarSerieComprobante(selectId) {
      try {
        const response = await this.fetchData("ajax/serie_comprobante.ajax.php");
        
        if (!response.status) throw new Error(response.message);
        
        const $select = $(`#${selectId}`).empty();
        $select.append('<option value="" disabled selected>Seleccionar comprobante</option>');
        
        response.data.forEach(serie => {
          if (serie.estado === 1) {
            $select.append(`
              <option value="${serie.id_serie}" data-serie="${serie.serie}">
                ${serie.nombre_tipo_comprobante}
              </option>
            `);
          }
        });
        
        $select.on("change", () => {
          const serieValue = $select.find("option:selected").data("serie") || "";
          $("#serie").val(serieValue);
        });
      } catch (error) {
        this.showError("Error al cargar series", error);
      }
    }
  
    async cargarTipoEncomienda(selectId, todas = false) {
      try {
        const response = await this.fetchData("ajax/tipo_encomienda.ajax.php");
        
        if (!response.status) throw new Error(response.message);
        
        const $select = $(`#${selectId}`).empty();
        $select.append(todas ? 
          '<option value="">Todas</option>' : 
          '<option value="" disabled selected>Seleccionar tipo</option>');
        
        response.data.forEach(tipo => {
          if (tipo.estado === 1) {
            $select.append(`<option value="${tipo.id_tipo_encomienda}">${tipo.nombre}</option>`);
          }
        });
      } catch (error) {
        this.showError("Error al cargar tipos de encomienda", error);
      }
    }
  
    async cargarTransportistas(selectId) {
      try {
        const response = await this.fetchData("ajax/transporte.ajax.php");
        
        if (!response.status) throw new Error(response.message);
        
        const $select = $(`#${selectId}`).empty();
        $select.append('<option value="">Seleccionar transportista</option>');
        
        response.data.forEach(transportista => {
          $select.append(`<option value="${transportista.id_transportista}">${transportista.nombre_completo}</option>`);
        });
      } catch (error) {
        this.showError("Error al cargar transportistas", error);
      }
    }
  
    async cargarProductos($select) {
      try {
        const response = await this.fetchData("ajax/producto.ajax.php");
        
        if (!response.status) throw new Error(response.message);
        
        $select.empty().append('<option value="" disabled selected>Seleccionar producto</option>');
        
        response.data.forEach(producto => {
          $select.append(`
            <option value="${producto.id_producto}" data-peso="${producto.peso || 0}">
              ${producto.codigo} - ${producto.nombre}
            </option>
          `);
        });
      } catch (error) {
        this.showError("Error al cargar productos", error);
      }
    }
  
    // ================ MÉTODOS DE CÁLCULO ================
  
    async calcularCostoEnvio() {
      try {
        const origen = $("#formNuevoEnvio [name='id_sucursal_origen']").val();
        const destino = $("#formNuevoEnvio [name='id_sucursal_destino']").val();
        const tipo = $("#formNuevoEnvio [name='id_tipo_encomienda']").val();
        const pesoTotal = parseFloat($("#pesoTotal").val()) || 0;
        
        if (!origen || !destino || !tipo) {
          throw new Error("Complete origen, destino y tipo de envío");
        }
        
        if (pesoTotal <= 0) {
          throw new Error("El peso total debe ser mayor a cero");
        }
        
        const response = await this.fetchData("ajax/envios.ajax.php", "GET", {
          action: 'calcularCosto',
          origen,
          destino,
          tipo,
          peso: pesoTotal
        });
        
        if (response.status) {
          $("#costoEnvio").val(response.data.costo.toFixed(2));
          this.showSuccess(`
            Costo estimado: S/ ${response.data.costo.toFixed(2)}<br>
            Tiempo estimado: ${response.data.tiempo_estimado} horas
          `);
        } else {
          throw new Error(response.message);
        }
      } catch (error) {
        this.showError("Error al calcular costo", error);
      }
    }
  
    // ================ MÉTODOS DE RENDERIZADO ================
  
    renderTablaEnvios(envios) {
      const tbody = $("#tablaEnvios tbody").empty();
      
      envios.forEach((envio, index) => {
        const fila = `
          <tr>
            <td>${index + 1}</td>
            <td>${envio.codigo_envio}</td>
            <td>${envio.sucursal_origen}</td>
            <td>${envio.sucursal_destino}</td>
            <td>${envio.tipo_encomienda}</td>
            <td>${envio.fecha_creacion ? new Date(envio.fecha_creacion).toLocaleString() : 'Pendiente'}</td>
            <td><span class="${this.getClaseEstado(envio.estado)}">${envio.estado.replace('_', ' ')}</span></td>
            <td>${envio.transportista || 'No asignado'}</td>
            <td class="text-center">${this.renderAccionesEnvio(envio)}</td>
          </tr>`;
        
        tbody.append(fila);
      });
  
      this.initDataTable("#tablaEnvios");
    }
  
    renderDetalleEnvio(data) {
      const { envio, paquetes, seguimiento, documentos } = data;
      
      // Información básica
      $("#codigoEnvio, #detalleCodigo").text(envio.codigo_envio);
      $("#detalleOrigen").text(envio.sucursal_origen);
      $("#detalleDestino").text(envio.sucursal_destino);
      $("#detalleTipo").text(envio.tipo_encomienda);
      $("#detalleTransportista").text(envio.transportista || 'No asignado');
      $("#detalleFechaCreacion").text(new Date(envio.fecha_creacion).toLocaleString());
      $("#detalleFechaEnvio").text(envio.fecha_envio ? new Date(envio.fecha_envio).toLocaleString() : 'Pendiente');
      $("#detalleFechaRecepcion").text(envio.fecha_recepcion ? new Date(envio.fecha_recepcion).toLocaleString() : 'Pendiente');
      $("#detalleEstado").html(`<span class="${this.getClaseEstado(envio.estado)}">${envio.estado.replace('_', ' ')}</span>`);
      
      // Paquetes
      const tbodyPaquetes = $("#tablaPaquetes tbody").empty();
      $("#totalPaquetesDetalle").text(paquetes.length);
      
      paquetes.forEach(paquete => {
        const fila = `
          <tr>
            <td>${paquete.codigo_paquete}</td>
            <td>${paquete.descripcion}</td>
            <td>${paquete.peso} kg</td>
            <td>${paquete.volumen ? paquete.volumen + ' m³' : 'N/A'}</td>
            <td><span class="${this.getClaseEstadoPaquete(paquete.estado)}">${paquete.estado}</span></td>
          </tr>`;
        tbodyPaquetes.append(fila);
      });
      
      // Seguimiento
      const timeline = $("#timelineSeguimiento").empty();
      seguimiento.forEach(seguimiento => {
        const template = $("#templateSeguimiento").html()
          .replace("{{estado}}", seguimiento.estado_nuevo.replace('_', ' '))
          .replace("{{fecha}}", new Date(seguimiento.fecha_registro).toLocaleString())
          .replace("{{observaciones}}", seguimiento.observaciones || 'Sin observaciones')
          .replace("{{usuario}}", seguimiento.usuario || 'Sistema');
        timeline.append(template);
      });
      
      // Documentos
      const listaDocumentos = $("#listaDocumentos").empty();
      documentos.forEach(documento => {
        const template = $("#templateDocumento").html()
          .replace("{{tipo}}", documento.tipo_documento)
          .replace("{{fecha}}", new Date(documento.fecha_subida).toLocaleDateString())
          .replace("{{url}}", documento.ruta_archivo)
          .replace("{{id}}", documento.id_documento);
        
        const $doc = $(template);
        $doc.find(".btnVerDocumento").attr("href", documento.ruta_archivo);
        $doc.find(".btnEliminarDocumento").click(() => this.eliminarDocumento(documento.id_documento));
        
        listaDocumentos.append($doc);
      });
    }
  
    renderAccionesEnvio(envio) {
      return `
        <div class="dropdown">
          <button class="btn btn-sm btn-outline-primary dropdown-toggle py-1 px-2" type="button" 
                  data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-cog"></i>
          </button>
          
          <ul class="dropdown-menu shadow-sm">
            <li>
              <a class="dropdown-item d-flex align-items-center btnDetalleEnvio" href="#" data-id="${envio.id_envio}">
                <i class="fas fa-eye text-primary me-2"></i>
                <span>Ver Detalle</span>
              </a>
            </li>
            
            <li>
              <a class="dropdown-item d-flex align-items-center btnCambiarEstado" href="#" data-id="${envio.id_envio}">
                <i class="fas fa-exchange-alt text-warning me-2"></i>
                <span>Cambiar Estado</span>
              </a>
            </li>
            
            ${['PENDIENTE', 'PREPARACION'].includes(envio.estado) ? `
            <li>
              <a class="dropdown-item d-flex align-items-center btnCancelarEnvio" href="#" data-id="${envio.id_envio}">
                <i class="fas fa-times text-danger me-2"></i>
                <span>Cancelar Envío</span>
              </a>
            </li>
            ` : ''}
            
            <li><hr class="dropdown-divider"></li>
            
            <li>
              <a class="dropdown-item d-flex align-items-center" href="#">
                <i class="fas fa-print text-secondary me-2"></i>
                <span>Imprimir</span>
              </a>
            </li>
          </ul>
        </div>`;
    }
  
    // ================ MÉTODOS DE UTILIDAD ================
  
    async fetchData(url, method = "GET", data = null) {
        try {
            const options = {
                method,
                headers: {},
                cache: "no-cache",
                credentials: 'same-origin'
            };
            
            // Manejo diferente para GET vs otros métodos
            if (method === "GET" && data) {
                // Para GET, convertir a query string
                const params = new URLSearchParams();
                for (const key in data) {
                    if (data[key] !== undefined && data[key] !== null) {
                        params.append(key, data[key]);
                    }
                }
                url = `${url}?${params.toString()}`;
            } else if (data) {
                // Para POST/PUT, etc.
                if (data instanceof FormData) {
                    // No establecer Content-Type para FormData, el navegador lo hará con el boundary correcto
                    options.body = data;
                } else {
                    options.headers["Content-Type"] = "application/json";
                    options.body = JSON.stringify(data);
                }
            }
            
            const response = await fetch(url, options);
            
            if (!response.ok) {
                const errorData = await response.json().catch(() => null);
                throw new Error(errorData?.message || `Error HTTP ${response.status}`);
            }
            
            return await response.json();
        } catch (error) {
            console.error("Fetch error:", { url, method, error: error.message });
            throw error;
        }
    }
  
    getClaseEstado(estado) {
      const estados = {
        'PENDIENTE': "badge bg-secondary",
        'PREPARACION': "badge bg-warning text-dark",
        'EN_TRANSITO': "badge bg-primary",
        'EN_REPARTO': "badge bg-info",
        'ENTREGADO': "badge bg-success",
        'CANCELADO': "badge bg-danger",
        'RECHAZADO': "badge bg-danger"
      };
      return estados[estado] || "badge bg-secondary";
    }
  
    getClaseEstadoPaquete(estado) {
      const estados = {
        'BUENO': "badge bg-success",
        'DANADO': "badge bg-danger",
        'PERDIDO': "badge bg-dark",
        'ENTREGADO': "badge bg-primary"
      };
      return estados[estado] || "badge bg-secondary";
    }
  
    initDataTable(selector) {
      if ($.fn.DataTable.isDataTable(selector)) {
        $(selector).DataTable().destroy();
      }
      
      $(selector).DataTable({
        autoWidth: false,
        responsive: true,
        language: {
          url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
        }
      });
    }
  
    validateEnvioForm() {
      const form = $("#formNuevoEnvio");
      let isValid = true;
      
      // Validar campos obligatorios
      if (!form.find("[name='id_sucursal_origen']").val()) {
        $("#error_origen").html("Seleccione sucursal origen").addClass("text-danger");
        isValid = false;
      }
      
      if (!form.find("[name='id_sucursal_destino']").val()) {
        $("#error_destino").html("Seleccione sucursal destino").addClass("text-danger");
        isValid = false;
      }
      
      if (!form.find("[name='id_tipo_encomienda']").val()) {
        $("#error_tipo").html("Seleccione tipo de encomienda").addClass("text-danger");
        isValid = false;
      }
      
      // Validar al menos un paquete
      if (this.contadorPaquetes === 0) {
        this.showWarning("Debe agregar al menos un paquete al envío");
        isValid = false;
      }
      
      return isValid;
    }
  
    resetForm() {
      $("#formNuevoEnvio")[0].reset();
      $("#contenedorPaquetes").empty();
      this.contadorPaquetes = 0;
      this.contadorItems = 0;
      $(".error-message").html("").removeClass("text-danger");
    }
  
    fechaHoraActual() {
      const now = new Date();
      const timezoneOffset = now.getTimezoneOffset() * 60000;
      const localISOTime = new Date(now - timezoneOffset).toISOString().slice(0, 16);
      $("#fecha_estimada_entrega").val(localISOTime);
    }
  
    generarCodigoEnvio() {
      const fecha = new Date();
      const year = fecha.getFullYear().toString().slice(-2);
      const month = (fecha.getMonth() + 1).toString().padStart(2, '0');
      const day = fecha.getDate().toString().padStart(2, '0');
      const random = Math.floor(1000 + Math.random() * 9000);
      return `ENV${year}${month}${day}${random}`;
    }
  
    async filtrarEnvios(e) {
      e.preventDefault();
      
      try {
        const filtros = {
          origen: $("#filtroOrigen").val(),
          destino: $("#filtroDestino").val(),
          tipo: $("#filtroTipo").val(),
          estado: $("#filtroEstado").val()
        };
        
        await this.mostrarEnvios(filtros);
      } catch (error) {
        this.showError("Error al filtrar envíos", error);
      }
    }
  
    async cancelarEnvio(idEnvio) {
      try {
        const result = await Swal.fire({
          title: "¿Cancelar este envío?",
          text: "Esta acción no se puede deshacer",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Sí, cancelar"
        });
        
        if (result.isConfirmed) {
          await this.actualizarEstadoEnvio(idEnvio, "CANCELADO", "Envío cancelado por el usuario");
        }
      } catch (error) {
        this.showError("Error al cancelar envío", error);
      }
    }
  
    async eliminarDocumento(idDocumento) {
      try {
        const result = await Swal.fire({
          title: "¿Eliminar documento?",
          text: "Esta acción no se puede deshacer",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Sí, eliminar"
        });
        
        if (result.isConfirmed) {
          const response = await this.fetchData("ajax/envios.ajax.php", "POST", {
            action: "eliminarDocumento",
            id_documento: idDocumento
          });
          
          if (response.status) {
            this.showSuccess("Documento eliminado");
            await this.mostrarDetalleEnvio(this.envioActual.id_envio);
          } else {
            throw new Error(response.message);
          }
        }
      } catch (error) {
        this.showError("Error al eliminar documento", error);
      }
    }
  
    imprimirGuia() {
      if (!this.envioActual) return;
      window.open(`extensiones/guia_remision.php?action=imprimirGuia&comprobante=guia_remision&id=${this.envioActual.id_envio}`, "_blank");
    }
  
    showSuccess(message) {
      Swal.fire("¡Éxito!", message, "success");
    }
  
    showWarning(message) {
      Swal.fire("Advertencia", message, "warning");
    }
  
    showError(title, error) {
      console.error(title, error);
      Swal.fire("Error", `${title}: ${error.message}`, "error");
    }
  }
  
  // Inicialización cuando el DOM esté listo
  $(document).ready(() => {
    window.enviosManager = new EnviosManager();
  });