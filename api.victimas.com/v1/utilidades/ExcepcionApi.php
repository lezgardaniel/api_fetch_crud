<?php

/**
 * Excepcion personalizada para el envio del estado
 */
class ExcepcionApi extends Exception
{
    public $estado;
// 400: Mala petición. El servidor no puede devolver una respuesta debido a un error del cliente.
    public function __construct($estado, $mensaje, $codigo = 400)
    {
        $this->estado = $estado;
        $this->message = $mensaje;
        $this->code = $codigo;
    }

}