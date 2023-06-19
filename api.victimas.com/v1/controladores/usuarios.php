<?php
include_once 'datos/ConexionBD.php';

class usuarios
{
    // Nombre de la tabla
    const NOMBRE_TABLA = "usuario";
    // Campos de la tabla
    const ID_USUARIO = "idUsuario";
    const NOMBRE = "nombre";
    const CONTRASENA = "contrasena";
    const CORREO = "correo";
    const CLAVE_API = "claveApi";

    // Constantes de estado
    const ESTADO_CREACION_EXITOSA = 1;
    const ESTADO_CREACION_FALLIDA = 2;
    const ESTADO_ERROR_BD = 3;
    const ESTADO_AUSENCIA_CLAVE_API = 4;
    const ESTADO_CLAVE_NO_AUTORIZADA = 5;
    const ESTADO_URL_INCORRECTA = 6;
    const ESTADO_FALLA_DESCONOCIDA = 7;
    const ESTADO_PARAMETROS_INCORRECTOS = 8;

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
            throw new ExcepcionApi(self::ESTADO_PARAMETROS_INCORRECTOS, "faltan parámetros", 400);
        }
    }

    //PETICION POST
    public static function post($peticion)
    {
        if ($peticion[0] == 'registro') {
            return self::registrar();
        } else if ($peticion[0] == 'login') {
            return self::loguear();
        } else {
            throw new ExcepcionApi(self::ESTADO_URL_INCORRECTA, "Url mal formada", 400);
        }
    }

    //PETICION PUT
    public static function put($peticion)
    {
        $idUsuario = usuarios::autorizar();

        if (!empty($peticion[0])) {
            $body = file_get_contents('php://input');
            $usuario = json_decode($body);

            if (self::actualizar($idUsuario, $usuario, $peticion[0]) > 0) {
                http_response_code(200);
                return [
                    "estado" => self::ESTADO_CREACION_EXITOSA,
                    "mensaje" => "Registro actualizado correctamente"
                ];
            } else {
                throw new ExcepcionApi(
                    self::ESTADO_CREACION_FALLIDA,
                    "El usuario que intentas actualizar no existe",
                    404
                );
            }
        } else {
            throw new ExcepcionApi(self::ESTADO_PARAMETROS_INCORRECTOS, "Falta id", 422);
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
                    "estado" => self::ESTADO_CREACION_EXITOSA,
                    "mensaje" => "Registro eliminado correctamente"
                ];
            } else {
                throw new ExcepcionApi(
                    self::ESTADO_CREACION_FALLIDA,
                    "El usuario que intentas eliminar no existe",
                    404
                );
            }
        } else {
            throw new ExcepcionApi(self::ESTADO_PARAMETROS_INCORRECTOS, "Falta id", 422);
        }
    }


 //******************************************************************* */
    /**
     * Crea un nuevo usuario en la tabla 'usuario'
     */
    public static function registrar()
    {
        $cuerpo = file_get_contents('php://input');
        $usuario = json_decode($cuerpo);

        $resultado = self::crear($usuario);

        switch ($resultado) {
            case self::ESTADO_CREACION_EXITOSA:
                http_response_code(200);
                return
                    [
                        "estado" => self::ESTADO_CREACION_EXITOSA,
                        "mensaje" => utf8_encode("Registro con exito!")
                    ];
                break;
            case self::ESTADO_CREACION_FALLIDA:
                throw new ExcepcionApi(self::ESTADO_CREACION_FALLIDA, "Ha ocurrido un error");
                break;
            default:
                throw new ExcepcionApi(self::ESTADO_FALLA_DESCONOCIDA, "Falla desconocida", 400);
        }
    }

    /**
     * Crea un nuevo usuario en la tabla "usuario"
     */
    private static function crear($datosUsuario)
    {
        $nombre = $datosUsuario->nombre;
        $contrasena = $datosUsuario->contrasena;
        $contrasenaEncriptada = self::encriptarContrasena($contrasena);
        $correo = $datosUsuario->correo;
        $claveApi = self::generarClaveApi();

        try {
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

            // Sentencia INSERT
            $consulta = "INSERT INTO " . self::NOMBRE_TABLA . " ( " .
                self::NOMBRE . "," .
                self::CONTRASENA . "," .
                self::CLAVE_API . "," .
                self::CORREO . ")" .
                " VALUES(?,?,?,?)";

            $sentencia = $pdo->prepare($consulta);

            $sentencia->bindParam(1, $nombre);
            $sentencia->bindParam(2, $contrasenaEncriptada);
            $sentencia->bindParam(3, $claveApi);
            $sentencia->bindParam(4, $correo);

            $resultado = $sentencia->execute();

            if ($resultado) {
                return self::ESTADO_CREACION_EXITOSA;
            } else {
                return self::ESTADO_CREACION_FALLIDA;
            }
        } catch (PDOException $e) {
            throw new ExcepcionApi(self::ESTADO_ERROR_BD, $e->getMessage());
        }
    }

    /**
     * Protege la contrasena con un algoritmo de encriptado
     */
    private static function encriptarContrasena($contrasenaPlana)
    {
        if ($contrasenaPlana)
            return password_hash($contrasenaPlana, PASSWORD_DEFAULT);
        else return null;
    }

    private static function generarClaveApi()
    {
        return md5(microtime() . rand());
    }

    /**
     * Inicia sesion si los datos proporcionados y la claveAPI enviada son correctos
     */
    public static function loguear()
    {
        $respuesta = array();

        $body = file_get_contents('php://input');
        $usuario = json_decode($body);

        $correo = $usuario->correo;
        $contrasena = $usuario->contrasena;

        if (self::autenticar($correo, $contrasena)) {
            $usuarioBD = self::obtenerUsuarioPorCorreo($correo);

            if ($usuarioBD != NULL) {
                http_response_code(200);
                $respuesta["nombre"] = $usuarioBD["nombre"];
                $respuesta["correo"] = $usuarioBD["correo"];
                $respuesta["claveApi"] = $usuarioBD["claveApi"];
                return ["estado" => 1, "usuario" => $respuesta];
            } else {
                throw new ExcepcionApi(
                    self::ESTADO_FALLA_DESCONOCIDA,
                    "Ha ocurrido un error"
                );
            }
        } else {
            throw new ExcepcionApi(
                self::ESTADO_PARAMETROS_INCORRECTOS,
                utf8_encode("Correo o contrasenia invalidos")
            );
        }
    }

    /**
     * Autentifica el usuario mediante el correo y contrasena proporcionados
     */
    public static function autenticar($correo, $contrasena)
    {
        $consulta = "SELECT contrasena FROM " . self::NOMBRE_TABLA .
            " WHERE " . self::CORREO . "=?";
        try {
            $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($consulta);
            $sentencia->bindParam(1, $correo);
            $sentencia->execute();

            if ($sentencia) {
                $resultado = $sentencia->fetch();

                if (self::validarContrasena($contrasena, $resultado['contrasena'])) {
                    return true;
                } else return false;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            throw new ExcepcionApi(self::ESTADO_ERROR_BD, $e->getMessage());
        }
    }

    /**
     * Verifica que la contrasena sea la correcta
     */
    public static function validarContrasena($contrasenaPlana, $contrasenaHash)
    {
        return password_verify($contrasenaPlana, $contrasenaHash);
    }


    /**
     * Otorga los permisos a un usuario para que acceda a los recursos
     */
    public static function autorizar()
    {
        $cabeceras = apache_request_headers();

        if (isset($cabeceras["Authorization"])) {
            $claveApi = $cabeceras["Authorization"];

            if (usuarios::validarClaveApi($claveApi)) {
                return usuarios::obtenerIdUsuario($claveApi);
            } else {
                throw new ExcepcionApi(
                    self::ESTADO_CLAVE_NO_AUTORIZADA,
                    "Clave de API no autorizada",
                    401
                );
            }
        } else {
            throw new ExcepcionApi(
                self::ESTADO_AUSENCIA_CLAVE_API,
                utf8_encode("Se requiere Clave del API para autenticacion")
            );
        }
    }

    /**
     * Comprueba la existencia de la clave para la api
     */
    public static function validarClaveApi($claveApi)
    {
        $consulta = "SELECT COUNT(" . self::ID_USUARIO . ")" .
            " FROM " . self::NOMBRE_TABLA .
            " WHERE " . self::CLAVE_API . "=?";

        $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($consulta);
        $sentencia->bindParam(1, $claveApi);
        $sentencia->execute();

        return $sentencia->fetchColumn(0) > 0;
    }


    /**
     * Obtiene el usuario mediante el correo
     */
    public static function obtenerUsuarioPorCorreo($correo)
    {
        $consulta = "SELECT " .
            self::NOMBRE . "," .
            self::CONTRASENA . "," .
            self::CORREO . "," .
            self::CLAVE_API .
            " FROM " . self::NOMBRE_TABLA .
            " WHERE " . self::CORREO . "=?";

        $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($consulta);
        $sentencia->bindParam(1, $correo);

        if ($sentencia->execute())
            return $sentencia->fetch(PDO::FETCH_ASSOC);
        else
            return null;
    }

    /**
     * Obtiene el valor de la columna "idUsuario" basado en la clave de api
     */
    public static function obtenerIdUsuario($claveApi)
    {
        $consulta = "SELECT " . self::ID_USUARIO .
            " FROM " . self::NOMBRE_TABLA .
            " WHERE " . self::CLAVE_API . "=?";

        $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($consulta);
        $sentencia->bindParam(1, $claveApi);

        if ($sentencia->execute()) {
            $resultado = $sentencia->fetch();
            return $resultado['idUsuario'];
        } else
            return null;
    }


    // LISTAR TODOS LOS USUARIOS
    public static function listarTodos()
    {
        $consulta = "SELECT " .
            self::ID_USUARIO . "," .
            self::NOMBRE . "," .
            self::CONTRASENA . "," .
            self::CORREO . "," .
            self::CLAVE_API .
            " FROM " . self::NOMBRE_TABLA;

        $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($consulta);

        if ($sentencia->execute()) {
            $resultados = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            return !empty($resultados) ? $resultados : null;
        } else
            return null;
    }

    // LISTAR USUARIOS POR ID
    public static function listarPorId($idUsuario)
    {
        $consulta = "SELECT " .
            self::ID_USUARIO . "," .
            self::NOMBRE . "," .
            self::CONTRASENA . "," .
            self::CORREO . "," .
            self::CLAVE_API .
            " FROM " . self::NOMBRE_TABLA .
            " WHERE " . self::ID_USUARIO . " = :idUsuario";

        $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($consulta);
        $sentencia->bindParam(':idUsuario', $idUsuario);

        if ($sentencia->execute())
            return $sentencia->fetch(PDO::FETCH_ASSOC);
        else
            return null;
    }

    // LISTAR USUARIOS POR RANGOS DE ID
    private static function listarPorRango($inicio, $fin)
    {
        $consulta = "SELECT " .
            self::ID_USUARIO . "," .
            self::NOMBRE . "," .
            self::CONTRASENA . "," .
            self::CORREO . "," .
            self::CLAVE_API .
            " FROM " . self::NOMBRE_TABLA .
            " WHERE " . self::ID_USUARIO . " >= :inicio AND " . self::ID_USUARIO . " <= :fin";

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
    private static function actualizar($idUsuario, $usuario)
    {
        $nombre = $usuario->nombre;
        $contrasena = $usuario->contrasena;
        $contrasenaEncriptada = self::encriptarContrasena($contrasena);
        $correo = $usuario->correo;

        try {
            // Creando consulta UPDATE
            $consulta = "UPDATE " . self::NOMBRE_TABLA .
                " SET " . self::NOMBRE . "=?," .
                self::CONTRASENA . "=?," .
                self::CORREO . "=?" .
                " WHERE " . self::ID_USUARIO . "=?";

            // Preparar la sentencia
            $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($consulta);

            $sentencia->bindParam(1, $nombre);
            $sentencia->bindParam(2, $contrasenaEncriptada);
            $sentencia->bindParam(3, $correo);
            $sentencia->bindParam(4, $idUsuario);
            
            // Ejecutar la sentencia
            $sentencia->execute();

            return $sentencia->rowCount();
        } catch (PDOException $e) {
            throw new ExcepcionApi(self::ESTADO_ERROR_BD, $e->getMessage());
        }
    }

    // ELIMINAR REGISTRO POR ID
    private static function eliminar($idUsuario)
    {
        try {
            // Sentencia DELETE
            $consulta = "DELETE FROM " . self::NOMBRE_TABLA .
                " WHERE " . self::ID_USUARIO . " =?";

            // Preparar la sentencia
            $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($consulta);
            $sentencia->bindParam(1, $idUsuario);
            $sentencia->execute();

            return $sentencia->rowCount();
        } catch (PDOException $e) {
            throw new ExcepcionApi(self::ESTADO_ERROR_BD, $e->getMessage());
        }
    }
}
