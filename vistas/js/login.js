$(document).ready(function () {
    // Validar formulario antes de enviar
    $("form").on("submit", function (e) {
        e.preventDefault();
        let usuario = $("[name='ingUsuario']").val();
        let password = $("[name='ingPassword']").val();

        if (usuario == "" || password == "") {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Usuario y contraseña son obligatorios'
            });
            return false;
        }

        // Enviar formulario
        $.ajax({
            url: "ajax/usuarios.ajax.php",
            type: "POST",
            data: $(this).serialize(),
            success: function (respuesta) {
            let data = JSON.parse(respuesta);
            console.log(data);
            if (data.status) {
                window.location = "inicio";
            } else {
                Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Usuario o contraseña incorrectos'
                });
            }
            }
        });
    });

});