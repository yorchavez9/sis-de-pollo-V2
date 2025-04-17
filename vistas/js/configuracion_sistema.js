$(document).ready(function () {
    // Cargar configuración existente
    cargarConfiguracion();



    // Validación manual del formulario
    $("#form_configuracion").on("submit", function (e) {
        e.preventDefault();

        // Resetear errores
        $(".text-danger").text("");

        // Validar campos
        let isValid = true;

        if (!$("#nombre_empresa").val()) {
            $("#error_nombre_empresa").text("Ingrese el nombre de la empresa");
            isValid = false;
        }

        const ruc = $("#ruc").val();
        if (!ruc) {
            $("#error_ruc").text("Ingrese el RUC");
            isValid = false;
        } else if (ruc.length !== 11 || !/^\d+$/.test(ruc)) {
            $("#error_ruc").text("El RUC debe tener 11 dígitos");
            isValid = false;
        }

        if (!$("#direccion").val()) {
            $("#error_direccion").text("Ingrese la dirección");
            isValid = false;
        }

        const telefono = $("#telefono").val();
        if (!telefono) {
            $("#error_telefono").text("Ingrese el teléfono");
            isValid = false;
        } else if (telefono.length < 9) {
            $("#error_telefono").text("Mínimo 9 caracteres");
            isValid = false;
        }

        const email = $("#email").val();
        if (!email) {
            $("#error_email").text("Ingrese el email");
            isValid = false;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            $("#error_email").text("Ingrese un email válido");
            isValid = false;
        }

        const impuesto = parseFloat($("#impuesto").val());
        if (isNaN(impuesto)) {
            $("#error_impuesto").text("Ingrese el porcentaje de impuesto");
            isValid = false;
        } else if (impuesto < 0 || impuesto > 100) {
            $("#error_impuesto").text("El impuesto debe estar entre 0 y 100");
            isValid = false;
        }

        if (isValid) {
            guardarConfiguracion();
        }
    });

    // Previsualización del logo
    $("#logo").change(function () {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                $("#preview_logo").html(`<img src="${e.target.result}" class="img-thumbnail" style="max-height: 100px;">`);
            }
            reader.readAsDataURL(file);
        }
    });
});

async function cargarConfiguracion() {
    try {
        const response = await fetch("ajax/configuracion.sistema.ajax.php", {
            method: "GET",
            headers: { "Accept": "application/json" }
        });
        if (!response.ok) throw new Error("Error al cargar configuración");

        const data = await response.json();
        console.log(data);

        if (data.status) {
            const config = data.data;
            $("#nombre_empresa").val(config.nombre_empresa);
            $("#ruc").val(config.ruc);
            $("#direccion").val(config.direccion);
            $("#telefono").val(config.telefono);
            $("#email").val(config.email);
            $("#moneda").val(config.moneda);
            $("#impuesto").val(config.impuesto);

            if (config.logo) {
                $("#preview_logo").html(`<img src="${config.logo}" class="img-thumbnail" style="max-height: 100px;">`);
                $(".empresa_logo").attr("src", config.logo);
                $("#login_icon").attr("src", config.logo);
                $("link[rel='shortcut icon']").attr("href", config.logo);
            }else{
                $("#login_icon").attr("src", "vistas/img/sistema/login-logo.png");
                $("link[rel='shortcut icon']").attr("href", "vistas/img/sistema/favicon.png");
            }

        }
    } catch (error) {
        console.error("Error:", error);
        Swal.fire("Error", "No se pudo cargar la configuración", "error");
    }
}

async function guardarConfiguracion() {
    const formData = new FormData($("#form_configuracion")[0]);
    formData.append("action", "guardar");

    try {
        const response = await fetch("ajax/configuracion.sistema.ajax.php", {
            method: "POST",
            body: formData
        });

        const result = await response.json();

        if (result.status) {
            Swal.fire("Éxito", result.message, "success");
            cargarConfiguracion();
        } else {
            Swal.fire("Error", result.message, "error");
        }
    } catch (error) {
        console.error("Error:", error);
        Swal.fire("Error", "Error al guardar la configuración", "error");
    }
}