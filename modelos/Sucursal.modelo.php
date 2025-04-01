<?php

require_once "Conexion.php";

class ModeloSucursal
{

    /*=============================================
    MOSTRAR SUCURSALES
    =============================================*/
    static public function mdlMostrarSucursales($tabla, $item, $valor)
    {
        try {
            if ($item != null) {
                $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item");
                $stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);
                $stmt->execute();
                return json_encode([
                    "status" => true,
                    "data" => $stmt->fetch()
                ]);
            } else {
                $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY id_sucursal DESC");
                $stmt->execute();
                return json_encode([
                    "status" => true,
                    "data" => $stmt->fetchAll()
                ]);
            }
        } catch (Exception $e) {
            return json_encode([
                "status" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    /*=============================================
    REGISTRAR SUCURSAL
    =============================================*/
    static public function mdlIngresarSucursal($tabla, $datos)
    {
        try {
            // Verificar si el código ya existe
            $stmt = Conexion::conectar()->prepare("SELECT COUNT(*) as count FROM $tabla WHERE codigo = :codigo");
            $stmt->bindParam(":codigo", $datos["codigo"], PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch();

            if ($result["count"] > 0) {
                return json_encode([
                    "status" => false,
                    "message" => "El código ya existe"
                ]);
            }

            // Insertar nueva sucursal
            $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(
                codigo, 
                nombre, 
                direccion, 
                ciudad, 
                telefono, 
                responsable, 
                es_principal
            ) VALUES (
                :codigo, 
                :nombre, 
                :direccion, 
                :ciudad, 
                :telefono, 
                :responsable, 
                :es_principal
            )");

            $stmt->bindParam(":codigo", $datos["codigo"], PDO::PARAM_STR);
            $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
            $stmt->bindParam(":direccion", $datos["direccion"], PDO::PARAM_STR);
            $stmt->bindParam(":ciudad", $datos["ciudad"], PDO::PARAM_STR);
            $stmt->bindParam(":telefono", $datos["telefono"], PDO::PARAM_STR);
            $stmt->bindParam(":responsable", $datos["responsable"], PDO::PARAM_STR);
            $stmt->bindParam(":es_principal", $datos["es_principal"], PDO::PARAM_INT);

            if ($stmt->execute()) {
                return json_encode([
                    "status" => true,
                    "message" => "Guardado con éxito"
                ]);
            } else {
                return json_encode([
                    "status" => false,
                    "message" => "Error al guardar los datos"
                ]);
            }
        } catch (Exception $e) {
            return json_encode([
                "status" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    /*=============================================
    EDITAR SUCURSAL
    =============================================*/
    static public function mdlEditarSucursal($tabla, $datos)
    {
        try {
            $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET 
                codigo = :codigo, 
                nombre = :nombre, 
                direccion = :direccion, 
                ciudad = :ciudad, 
                telefono = :telefono, 
                responsable = :responsable, 
                es_principal = :es_principal, 
                estado = :estado 
                WHERE id_sucursal = :id_sucursal");

            $stmt->bindParam(":codigo", $datos["codigo"], PDO::PARAM_STR);
            $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
            $stmt->bindParam(":direccion", $datos["direccion"], PDO::PARAM_STR);
            $stmt->bindParam(":ciudad", $datos["ciudad"], PDO::PARAM_STR);
            $stmt->bindParam(":telefono", $datos["telefono"], PDO::PARAM_STR);
            $stmt->bindParam(":responsable", $datos["responsable"], PDO::PARAM_STR);
            $stmt->bindParam(":es_principal", $datos["es_principal"], PDO::PARAM_INT);
            $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_INT);
            $stmt->bindParam(":id_sucursal", $datos["id_sucursal"], PDO::PARAM_INT);

            if ($stmt->execute()) {
                return json_encode([
                    "status" => true,
                    "message" => "Actualizado con éxito"
                ]);
            } else {
                return json_encode([
                    "status" => false,
                    "message" => "Error al actualizar los datos"
                ]);
            }
        } catch (Exception $e) {
            return json_encode([
                "status" => false,
                "message" => $e->getMessage()
            ]);
        }
    }


    /*=============================================
ACTUALIZAR SUCURSAL
=============================================*/
    static public function mdlActualizarSucursal($tabla, $datos)
    {
        try {
            $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET 
            codigo = :codigo, 
            nombre = :nombre, 
            direccion = :direccion, 
            ciudad = :ciudad, 
            telefono = :telefono, 
            responsable = :responsable, 
            es_principal = :es_principal 
            WHERE id_sucursal = :id_sucursal");

            $stmt->bindParam(":codigo", $datos["codigo"], PDO::PARAM_STR);
            $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
            $stmt->bindParam(":direccion", $datos["direccion"], PDO::PARAM_STR);
            $stmt->bindParam(":ciudad", $datos["ciudad"], PDO::PARAM_STR);
            $stmt->bindParam(":telefono", $datos["telefono"], PDO::PARAM_STR);
            $stmt->bindParam(":responsable", $datos["responsable"], PDO::PARAM_STR);
            $stmt->bindParam(":es_principal", $datos["es_principal"], PDO::PARAM_INT);
            $stmt->bindParam(":id_sucursal", $datos["id_sucursal"], PDO::PARAM_INT);

            if ($stmt->execute()) {
                return json_encode([
                    "status" => true,
                    "message" => "Actualizado con éxito"
                ]);
            } else {
                return json_encode([
                    "status" => false,
                    "message" => "Error al actualizar los datos"
                ]);
            }
        } catch (Exception $e) {
            return json_encode([
                "status" => false,
                "message" => $e->getMessage()
            ]);
        }
    }


    /*=============================================
    CAMBIAR ESTADO DE SUCURSAL
    =============================================*/
    static public function mdlCambiarEstadoSucursal($tabla, $datos)
    {
        try {
            $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET estado = :estado WHERE id_sucursal = :id_sucursal");
            $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_INT);
            $stmt->bindParam(":id_sucursal", $datos["id_sucursal"], PDO::PARAM_INT);

            if ($stmt->execute()) {
                return json_encode([
                    "status" => true,
                    "message" => "Estado actualizado con éxito"
                ]);
            } else {
                return json_encode([
                    "status" => false,
                    "message" => "Error al actualizar el estado"
                ]);
            }
        } catch (Exception $e) {
            return json_encode([
                "status" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    /*=============================================
    BORRAR SUCURSAL
    =============================================*/
    static public function mdlBorrarSucursal($tabla, $datos)
    {
        try {
            $stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE id_sucursal = :id_sucursal");
            $stmt->bindParam(":id_sucursal", $datos, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return json_encode([
                    "status" => true,
                    "message" => "Eliminado con éxito"
                ]);
            } else {
                return json_encode([
                    "status" => false,
                    "message" => "Error al eliminar los datos"
                ]);
            }
        } catch (Exception $e) {
            return json_encode([
                "status" => false,
                "message" => $e->getMessage()
            ]);
        }
    }
}
