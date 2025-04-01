function obtenerDatosSesion() {
    $.ajax({
        url: 'ajax/session.ajax.php',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
          /*   console.log(data); */
        },
        error: function(xhr, status, error) {
            console.error("Error al obtener los datos de la sesión:", error);
        }
    });
}

obtenerDatosSesion();
