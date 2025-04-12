async function obtenerSesion() {
    try {
        const response = await fetch('ajax/sesion.ajax.php?action=sesion', {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            },
            credentials: 'include'
        });

        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor');
        }

        const data = await response.json();

        if (data.status === false) {
            console.log('Mensaje del servidor:', data.message);
        } else {
            /* console.log('Datos completos de sesión:', data); */
            
            // Ejemplos de acceso a datos específicos
            /* console.log('Usuario:', data.usuario);
            console.log('Sucursal ID:', data.id_sucursal); */
            
            // Mostrar permisos en formato tabla para mejor visualización
           /*  console.table(data.permisos); */
            return data
        }
    } catch (error) {
        console.error('Error al obtener la sesión:', error);
    }
}

// Llamar a la función
/* obtenerSesion(); */
obtenerSesion().then(datosUsuario =>{
    if(datosUsuario){
        console.log(datosUsuario);
    }
})