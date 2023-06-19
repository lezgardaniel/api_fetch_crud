<?php

class denuncias
{
    // Nombre de la tabla
    const NOMBRE_TABLA = "denuncia";
    // Campos de la tabla
    const ID_DENUNCIA = "idDenuncia";
    const HECHOS = "hechos";
    const LUGAR = "lugar";
    const FECHA = "fecha";
    const RESPONSABLE = "responsable";
    const ID_VICTIMA = "idVictima";

    // Constantes de estado
    const CODIGO_EXITO = 1;
    const ESTADO_EXITO = 1;
    const ESTADO_ERROR = 2;
    const ESTADO_ERROR_BD = 3;
    const ESTADO_ERROR_PARAMETROS = 4;
    const ESTADO_NO_ENCONTRADO = 5;

    //PETICION GET
    public static function get($peticion)
    {
        if ($peticion == null) {
            return self::listarTodos();
        } else if (count($peticion) == 1) {
            return self::listarPorId($peticion[0]);
        } else if (count($peticion) == 2) {
            return self::listarPorRango($peticion[0], $peticion[1]);
        } else {
            throw new ExcepcionApi(self::ESTADO_ERROR_PARAMETROS, "faltan parámetros", 400);
        }
    }

    //PETICION POST
    public static function post()
    {
        $body = file_get_contents('php://input');
        $denuncia = json_decode($body);

        $idDenuncia = denuncias::crear($denuncia);

        http_response_code(201);
        return [
            "estado" => self::CODIGO_EXITO,
            "mensaje" => "Denuncia creada correctamente",
            "id" => $idDenuncia
        ];
    }

    //PETICION PUT
    public static function put($peticion)
    {
        if (!empty($peticion[0])) {
            $body = file_get_contents('php://input');
            $victima = json_decode($body);

            if (self::actualizar($victima, $peticion[0]) > 0) {
                http_response_code(200);
                return [
                    "estado" => self::ESTADO_EXITO,
                    "mensaje" => "Registro actualizado correctamente"
                ];
            } else {
                throw new ExcepcionApi(
                    self::ESTADO_ERROR,
                    "La denuncia que intentas actualizar no existe",
                    404
                );
            }
        } else {
            throw new ExcepcionApi(self::ESTADO_ERROR_PARAMETROS, "Falta id", 422);
        }
    }

    //PETICION DELETE
    public static function delete($peticion)
    {
        if (!empty($peticion[0])) {
            if (self::eliminar($peticion[0]) > 0) {
                http_response_code(200);
                return [
                    "estado" => self::ESTADO_EXITO,
                    "mensaje" => "Registro eliminado correctamente"
                ];
            } else {
                throw new ExcepcionApi(
                    self::ESTADO_NO_ENCONTRADO,
                    "La denuncia que intentas eliminar no existe",
                    404
                );
            }
        } else {
            throw new ExcepcionApi(self::ESTADO_ERROR, "Falta id", 422);
        }
    }


    //******************************************************************* */

    // AGREGAR REGISTRO
    private static function crear($datosDenuncia)
    {
        if ($datosDenuncia) {
            try {
                $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

                // Sentencia INSERT
                $consulta = "INSERT INTO " . self::NOMBRE_TABLA . " ( " .
                    self::HECHOS . "," .
                    self::LUGAR . "," .
                    self::FECHA . "," .
                    self::RESPONSABLE . "," .
                    self::ID_VICTIMA . ")" .
                    " VALUES(?,?,?,?,?)";

                // Preparar la sentencia
                $sentencia = $pdo->prepare($consulta);

                $sentencia->bindParam(1, $hechos);
                $sentencia->bindParam(2, $lugar);
                $sentencia->bindParam(3, $fecha);
                $sentencia->bindParam(4, $responsable);
                $sentencia->bindParam(5, $idVictima);

                $hechos = $datosDenuncia->hechos;
                $lugar = $datosDenuncia->lugar;
                $fecha = $datosDenuncia->fecha;
                $responsable = $datosDenuncia->responsable;
                $idVictima = $datosDenuncia->idVictima;

                $sentencia->execute();

                // Retornar en el ultimo id insertado
                return $pdo->lastInsertId();
            } catch (PDOException $e) {
                throw new ExcepcionApi(self::ESTADO_ERROR_BD, $e->getMessage());
            }
        } else {
            throw new ExcepcionApi(
                self::ESTADO_ERROR_PARAMETROS,
                utf8_encode("Error en existencia o sintaxis de parametros")
            );
        }
    }

    // LISTAR TODAS LAS DENUNCIAS
    public static function listarTodos()
    {
        $consulta = "SELECT * FROM " . self::NOMBRE_TABLA;

        $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($consulta);

        if ($sentencia->execute()) {
            $resultados = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            return !empty($resultados) ? $resultados : null;
        } else
            return null;
    }

    // LISTAR DENUNCIAS POR ID
    public static function listarPorId($idDenuncia)
    {
        $consulta = "SELECT * FROM " . self::NOMBRE_TABLA .
            " WHERE " . self::ID_DENUNCIA . " = :idDenuncia";

        $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($consulta);
        $sentencia->bindParam(':idDenuncia', $idDenuncia);

        if ($sentencia->execute())
            return $sentencia->fetch(PDO::FETCH_ASSOC);
        else
            return null;
    }

    // LISTAR DENUNCIAS POR RANGOS DE ID
    private static function listarPorRango($inicio, $fin)
    {
        $consulta = "SELECT * FROM " . self::NOMBRE_TABLA .
            " WHERE " . self::ID_DENUNCIA . " >= :inicio AND " . self::ID_DENUNCIA . " <= :fin";

        $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($consulta);
        $sentencia->bindParam(':inicio', $inicio);
        $sentencia->bindParam(':fin', $fin);

        if ($sentencia->execute()) {
            return $sentencia->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return null;
        }
    }


    // ACTUALIZAR REGISTRO
    private static function actualizar($denuncia, $idDenuncia)
    {
        try {
            // Creando consulta UPDATE
            $consulta = "UPDATE " . self::NOMBRE_TABLA .
                " SET " . self::HECHOS . "=?," .
                self::LUGAR . "=?," .
                self::FECHA . "=?," .
                self::RESPONSABLE . "=? " .
                " WHERE " . self::ID_DENUNCIA . "=?";

            // Preparar la sentencia
            $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($consulta);

            $sentencia->bindParam(1, $hechos);
            $sentencia->bindParam(2, $lugar);
            $sentencia->bindParam(3, $fecha);
            $sentencia->bindParam(4, $responsable);
            $sentencia->bindParam(5, $idDenuncia);
            
            $hechos = $denuncia->hechos;
            $lugar = $denuncia->lugar;
            $fecha = $denuncia->fecha;
            $responsable = $denuncia->responsable;

            // Ejecutar la sentencia
            $sentencia->execute();

            return $sentencia->rowCount();
        } catch (PDOException $e) {
            throw new ExcepcionApi(self::ESTADO_ERROR_BD, $e->getMessage());
        }
    }


    // ELIMINAR REGISTRO
    private static function eliminar($idDenuncia)
    {
        try {
            // Sentencia DELETE
            $consulta = "DELETE FROM " . self::NOMBRE_TABLA .
                " WHERE " . self::ID_DENUNCIA . "=?";

            // Preparar la sentencia
            $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($consulta);

            $sentencia->bindParam(1, $idDenuncia);
            $sentencia->execute();

            return $sentencia->rowCount();
        } catch (PDOException $e) {
            throw new ExcepcionApi(self::ESTADO_ERROR_BD, $e->getMessage());
        }
    }
}
