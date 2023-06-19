<?php

class victimas
{
    // Nombre de la tabla
    const NOMBRE_TABLA = "victima";
    // Campos de la tabla
    const ID_VICTIMA = "idVictima";
    const PRIMER_NOMBRE = "primerNombre";
    const PRIMER_APELLIDO = "primerApellido";
    const EDAD = "edad";
    const GENERO = "genero";
    const TELEFONO = "telefono";
    const ID_USUARIO = "idUsuario";

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
        $idUsuario = usuarios::autorizar();

        $body = file_get_contents('php://input');
        $victima = json_decode($body);

        $idVictima = victimas::crear($idUsuario, $victima);

        http_response_code(201);
        return [
            "estado" => self::CODIGO_EXITO,
            "mensaje" => "Victima creada correctamente",
            "id" => $idVictima
        ];
    }

    //PETICION PUT
    public static function put($peticion)
    {
        $idUsuario = usuarios::autorizar();

        if (!empty($peticion[0])) {
            $body = file_get_contents('php://input');
            $victima = json_decode($body);

            if (self::actualizar($idUsuario, $victima, $peticion[0]) > 0) {
                http_response_code(200);
                return [
                    "estado" => self::CODIGO_EXITO,
                    "mensaje" => "Registro actualizado correctamente"
                ];
            } else {
                throw new ExcepcionApi(
                    self::ESTADO_NO_ENCONTRADO,
                    "La victima que intentas actualizar no existe",
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
        $idUsuario = usuarios::autorizar();

        if (!empty($peticion[0])) {
            if (self::eliminar($idUsuario, $peticion[0]) > 0) {
                http_response_code(200);
                return [
                    "estado" => self::ESTADO_EXITO,
                    "mensaje" => "Registro eliminado correctamente"
                ];
            } else {
                throw new ExcepcionApi(
                    self::ESTADO_NO_ENCONTRADO,
                    "La victima que intentas eliminar no existe",
                    404
                );
            }
        } else {
            throw new ExcepcionApi(self::ESTADO_ERROR, "Falta id", 422);
        }
    }


    //******************************************************************* */

    // AGREGAR REGISTRO
    private static function crear($idUsuario, $datosVictima)
    {
        if ($datosVictima) {
            try {
                $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

                // Sentencia INSERT
                $consulta = "INSERT INTO " . self::NOMBRE_TABLA . " ( " .
                    self::PRIMER_NOMBRE . "," .
                    self::PRIMER_APELLIDO . "," .
                    self::EDAD . "," .
                    self::GENERO . "," .
                    self::TELEFONO . "," .
                    self::ID_USUARIO . ")" .
                    " VALUES(?,?,?,?,?,?)";

                // Preparar la sentencia
                $sentencia = $pdo->prepare($consulta);

                $sentencia->bindParam(1, $primerNombre);
                $sentencia->bindParam(2, $primerApellido);
                $sentencia->bindParam(3, $edad);
                $sentencia->bindParam(4, $genero);
                $sentencia->bindParam(5, $telefono);
                $sentencia->bindParam(6, $idUsuario);

                $primerNombre = $datosVictima->primerNombre;
                $primerApellido = $datosVictima->primerApellido;
                $edad = $datosVictima->edad;
                $genero = $datosVictima->genero;
                $telefono = $datosVictima->telefono;
                $idUsuario = $datosVictima->idUsuario;

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

    // LISTAR TODAS LAS VICTIMAS
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

    // LISTAR VICTIMAS POR ID
    public static function listarPorId($idVictima)
    {
        $consulta = "SELECT * FROM " . self::NOMBRE_TABLA .
            " WHERE " . self::ID_VICTIMA . " = :idVictima";
        
        $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($consulta);
        $sentencia->bindParam(':idVictima', $idVictima);

        if ($sentencia->execute())
            return $sentencia->fetch(PDO::FETCH_ASSOC);
        else
            return null;
    }

    // LISTAR VICTIMAS POR RANGOS DE ID
    private static function listarPorRango($inicio, $fin)
    {
        $consulta = "SELECT * FROM " . self::NOMBRE_TABLA .
            " WHERE " . self::ID_VICTIMA . " >= :inicio AND " . self::ID_VICTIMA . " <= :fin";
        
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
    private static function actualizar($idUsuario, $victima, $idVictima)
    {
        try {
            // Creando consulta UPDATE
            $consulta = "UPDATE " . self::NOMBRE_TABLA .
                " SET " . self::PRIMER_NOMBRE . "=?," .
                self::PRIMER_APELLIDO . "=?," .
                self::EDAD . "=?," .
                self::GENERO . "=?, " .
                self::TELEFONO . "=? " .
                " WHERE " . self::ID_VICTIMA . "=? AND " . self::ID_USUARIO . "=?";

            // Preparar la sentencia
            $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($consulta);

            $sentencia->bindParam(1, $primerNombre);
            $sentencia->bindParam(2, $primerApellido);
            $sentencia->bindParam(3, $edad);
            $sentencia->bindParam(4, $genero);
            $sentencia->bindParam(5, $telefono);
            $sentencia->bindParam(6, $idVictima);
            $sentencia->bindParam(7, $idUsuario);

            $primerNombre = $victima->primerNombre;
            $primerApellido = $victima->primerApellido;
            $edad = $victima->edad;
            $genero = $victima->genero;
            $telefono = $victima->telefono;

            // Ejecutar la sentencia
            $sentencia->execute();

            return $sentencia->rowCount();
        } catch (PDOException $e) {
            throw new ExcepcionApi(self::ESTADO_ERROR_BD, $e->getMessage());
        }
    }


    // ELIMINAR REGISTRO POR ID
    private static function eliminar($idUsuario, $idVictima)
    {
        try {
            // Sentencia DELETE
            $consulta = "DELETE FROM " . self::NOMBRE_TABLA .
                " WHERE " . self::ID_VICTIMA . "=? AND " .
                self::ID_USUARIO . "=?";

            // Preparar la sentencia
            $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($consulta);

            $sentencia->bindParam(1, $idVictima);
            $sentencia->bindParam(2, $idUsuario);
            $sentencia->execute();

            return $sentencia->rowCount();
        } catch (PDOException $e) {
            throw new ExcepcionApi(self::ESTADO_ERROR_BD, $e->getMessage());
        }
    }
}
