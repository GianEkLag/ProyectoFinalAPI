<?php
//class contactos
class categorias
{
    /*
    const NOMBRE_TABLA = "contacto";
    const ID_CONTACTO = "idContacto";
    const PRIMER_NOMBRE = "primerNombre";
    const PRIMER_APELLIDO = "primerApellido";
    const TELEFONO = "telefono";
    const CORREO = "correo";
    const ID_USUARIO = "idUsuario";
    */

    //Pedir los datos de la base de datos
    ////////////////////////////////////////////////
    const NOMBRE_TABLA = "categorias";
    const ID_PROD = "idProd";
    const CLAVE_PROD = "claveProd";
    const CATEGORIA = "categoria";
    const ID_USUARIO = "idUsuario";
    
    ///////////////////////////////////////////////

    const CODIGO_EXITO = 1;
    const ESTADO_EXITO = 1;
    const ESTADO_ERROR = 2;
    const ESTADO_ERROR_BD = 3;
    const ESTADO_ERROR_PARAMETROS = 4;
    const ESTADO_NO_ENCONTRADO = 5;
    
    public static function get($peticion)
    {
        $idUsuario = usuarios::autorizar();

        if (empty($peticion[0]))
            return self::obtenerContactos($idUsuario);
        else
            return self::obtenerContactos($idUsuario, $peticion[0]);
    }

    public static function post($peticion)
    {
        $idUsuario = usuarios::autorizar();

        $body = file_get_contents('php://input');

        //$contacto = json_decode($body); --> $contacto // $producto
        $categoriaa = json_decode($body);

        //$idContacto = contactos::crear($idUsuario, $contacto);
        $idCategoria = categorias::crear($idUsuario, $categoriaa);

        http_response_code(201);
        return [
            "estado" => self::CODIGO_EXITO,
            //"mensaje" => "Contacto creado",
            "mensaje" => "Nueva categoria creada",
            //"id" => $idContacto --> idContacto // idProducto
            "id" => $idCategoria
        ];

    }

    public static function put($peticion)
    {
        $idUsuario = usuarios::autorizar();

        if (!empty($peticion[0])) {
            $body = file_get_contents('php://input');

            //$contacto = json_decode($body);
            $categoriaa = json_decode($body);

            //if (self::actualizar($idUsuario, $contacto, $peticion[0]) > 0) {
            if (self::actualizar($idUsuario, $categoriaa, $peticion[0]) > 0) {
                http_response_code(200);
                return [
                    "estado" => self::CODIGO_EXITO,
                    "mensaje" => "Registro actualizado correctamente" 
                ];
            } else {
                throw new ExcepcionApi(self::ESTADO_NO_ENCONTRADO,
                    "El Producto al que intentas acceder no existe :(", 404);
            }
        } else {
            throw new ExcepcionApi(self::ESTADO_ERROR_PARAMETROS, "Le Falta el ID pa", 422);
        }
    }

    public static function delete($peticion)
    {
        $idUsuario = usuarios::autorizar();

        if (!empty($peticion[0])) {
            if (self::eliminar($idUsuario, $peticion[0]) > 0) {
                http_response_code(200);
                return [
                    "estado" => self::CODIGO_EXITO,
                    "mensaje" => "Categoria eliminado correctamente"
                ];
            } else {
                throw new ExcepcionApi(self::ESTADO_NO_ENCONTRADO,
                    "La categoria al que intentas acceder no existe", 404);
            }
        } else {
            throw new ExcepcionApi(self::ESTADO_ERROR_PARAMETROS, "Falta id del producto xD", 422);
        }

    }

    /**
     * Obtiene la colección de contactos o un solo contacto indicado por el identificador
     * @param int $idUsuario identificador del usuario
     * @param null $idContacto identificador del contacto (Opcional)
     * @return array registros de la tabla contacto
     * @throws Exception
     */
    //private function obtenerContactos($idUsuario, $idContacto = NULL)
    private static function obtenerContactos($idUsuario, $idCategoria = NULL)
    {
        try {
            //if (!$idContacto) {
            if (!$idCategoria) {
                $comando = "SELECT * FROM " . self::NOMBRE_TABLA .
                    " WHERE " . self::ID_USUARIO . "=?";

                // Preparar sentencia
                $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);
                // Ligar idUsuario
                $sentencia->bindParam(1, $idUsuario, PDO::PARAM_INT);

            } else {
                $comando = "SELECT * FROM " . self::NOMBRE_TABLA .
                  //" WHERE " . self::ID_CONTACTO . "=? AND " .
                    " WHERE " . self::ID_PROD . "=? AND " .
                    self::ID_USUARIO . "=?";

                // Preparar sentencia
                $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);
                // Ligar idContacto e idUsuario
                //$sentencia->bindParam(1, $idContacto, PDO::PARAM_INT);
                $sentencia->bindParam(1, $idCategoria, PDO::PARAM_INT);
                $sentencia->bindParam(2, $idUsuario, PDO::PARAM_INT);
            }

            // Ejecutar sentencia preparada
            if ($sentencia->execute()) {
                http_response_code(200);
                return
                    [
                        "estado" => self::ESTADO_EXITO,
                        "datos" => $sentencia->fetchAll(PDO::FETCH_ASSOC)
                    ];
            } else
                throw new ExcepcionApi(self::ESTADO_ERROR, "Se ha producido un error exitosamente");

        } catch (PDOException $e) {
            throw new ExcepcionApi(self::ESTADO_ERROR_BD, $e->getMessage());
        }
    }

    /**
     * Añade un nuevo contacto asociado a un usuario
     * @param int $idUsuario identificador del usuario
     * @param mixed $contacto datos del contacto
     * @return string identificador del contacto
     * @throws ExcepcionApi
     */
    //private function crear($idUsuario, $contacto)
    private static function crear($idUsuario, $categoriaa)
    {
        //if ($contacto) {
        if ($categoriaa) {
            try {

                $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

                // Sentencia INSERT
                /*
                $comando = "INSERT INTO " . self::NOMBRE_TABLA . " ( " .
                    self::PRIMER_NOMBRE . "," .
                    self::PRIMER_APELLIDO . "," .
                    self::TELEFONO . "," .
                    self::CORREO . "," .
                    self::ID_USUARIO . ")" .
                    " VALUES(?,?,?,?,?)"; */

                $comando = "INSERT INTO " . self::NOMBRE_TABLA . " ( " .
                self::CLAVE_PROD . "," .
                self::CATEGORIA . "," .
                self::ID_USUARIO . ")".
                " VALUES(?,?,?)";

                // Preparar la sentencia
                $sentencia = $pdo->prepare($comando);
                /*
                $sentencia->bindParam(1, $primerNombre);
                $sentencia->bindParam(2, $primerApellido);
                $sentencia->bindParam(3, $telefono);
                $sentencia->bindParam(4, $correo);
                $sentencia->bindParam(5, $idUsuario);*/

                $sentencia->bindParam(1, $claveProd);
                $sentencia->bindParam(2, $categoria);
                $sentencia->bindParam(3, $idUsuario); //Este se queda como tal

                /*
                $primerNombre =     $contacto->primerNombre;
                $primerApellido =   $contacto->primerApellido;
                $telefono =         $contacto->telefono;
                $correo =           $contacto->correo;*/

                $claveProd = $categoriaa->claveProd;
                $categoria = $categoriaa->categoria;
               
                $sentencia->execute();

                // Retornar en el último id insertado
                return $pdo->lastInsertId();

            } catch (PDOException $e) {
                throw new ExcepcionApi(self::ESTADO_ERROR_BD, $e->getMessage());
            }
        } else {
            throw new ExcepcionApi(
                self::ESTADO_ERROR_PARAMETROS,
                utf8_encode("Error en existencia o sintaxis de parametros"));
        }

    }

    /**
     * Actualiza el contacto especificado por idUsuario
     * @param int $idUsuario
     * @param object $contacto objeto con los valores nuevos del contacto
     * @param int $idContacto
     * @return PDOStatement
     * @throws Exception
     */
    //private function actualizar($idUsuario, $contacto, $idContacto)
    private static function actualizar($idUsuario, $categoriaa, $idCategoria)
    {
        try {
            // Creando consulta UPDATE

            /*$consulta = "UPDATE " . self::NOMBRE_TABLA .
                " SET " . self::PRIMER_NOMBRE . "=?," .
                self::PRIMER_APELLIDO . "=?," .
                self::TELEFONO . "=?," .
                self::CORREO . "=? " .
                " WHERE " . self::ID_CONTACTO . "=? AND " . self::ID_USUARIO . "=?"; */

            $consulta = "UPDATE " . self::NOMBRE_TABLA .
                " SET " . self::CLAVE_PROD . "=?," .
                self::CATEGORIA . "=?" .
                " WHERE " . self::ID_PROD . "=? AND " . self::ID_USUARIO . "=?";

            // Preparar la sentencia
            $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($consulta);
            /*
            $sentencia->bindParam(1, $primerNombre);
            $sentencia->bindParam(2, $primerApellido);
            $sentencia->bindParam(3, $telefono);
            $sentencia->bindParam(4, $correo);
            $sentencia->bindParam(5, $idContacto);
            $sentencia->bindParam(6, $idUsuario);*/

            $sentencia->bindParam(1, $claveProd);
            $sentencia->bindParam(2, $categoria);

            $sentencia->bindParam(3, $idCategoria);
            $sentencia->bindParam(4, $idUsuario);

            /*
            $primerNombre = $contacto->primerNombre;
            $primerApellido = $contacto->primerApellido;
            $telefono = $contacto->telefono;
            $correo = $contacto->correo;*/

            $claveProd = $categoriaa->claveProd;
            $categoria = $categoriaa->categoria;
     

            // Ejecutar la sentencia
            $sentencia->execute();

            return $sentencia->rowCount();

        } catch (PDOException $e) {
            throw new ExcepcionApi(self::ESTADO_ERROR_BD, $e->getMessage());
        }
    }


    /**
     * Elimina un contacto asociado a un usuario
     * @param int $idUsuario identificador del usuario
     * @param int $idContacto identificador del contacto
     * @return bool true si la eliminaci�n se pudo realizar, en caso contrario false
     * @throws Exception excepcion por errores en la base de datos
     */
    private static function eliminar($idUsuario, $idCategoria)
    {
        try {
            // Sentencia DELETE
            $comando = "DELETE FROM " . self::NOMBRE_TABLA .
                " WHERE " . self::ID_PROD . "=? AND " .
                self::ID_USUARIO . "=?";

            // Preparar la sentencia
            $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);

            $sentencia->bindParam(1, $idCategoria);
            $sentencia->bindParam(2, $idUsuario);

            $sentencia->execute();

            return $sentencia->rowCount();

        } catch (PDOException $e) {
            throw new ExcepcionApi(self::ESTADO_ERROR_BD, $e->getMessage());
        }
    }
}

