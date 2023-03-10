<?php

require_once 'conexion/conexion.php';
require_once 'respuestas.class.php';

class auth extends conexion{

  /* Login, recibimos el json que nos viene por POST*/
  public function login($json){
    $_respuestas = new respuestas;
    $datos = json_decode($json,true); //Convertimos el json en un array asociativo(true)
    if (!isset($datos['usuario']) || !isset($datos['password'])) {
      # Error en los campos
      return $_respuestas->error_400();
    }else{
      # Todo esta bien
      $usuario = $datos['usuario'];
      $password = $datos['password'];
      $password = parent::encriptar($password);//Encriptamos el password que nos envia el usuario para compararlo con el de la BBDD
      $datos = $this->obtenerDatosUsuario($usuario);
      if ($datos) {
        # SI existe usuario -> Verificaremos si la contraseña es correcta
        if ($password == $datos[0]['Password']) {
          //Verificamos si el usuario esta activo en el campo de la BBDD Estado
          if ($datos[0]["Estado"] == "Activo") {
            # Creamos el token
            $verificar = $this->insertarToken($datos[0]['UsuarioId']);
            if($verificar){
              //se guardo el token
              $result = $_respuestas->response;
              $result["result"] = array(
                "token" => $verificar
              );
              return $result;
            }else{
              //No se guardo el token
              return $_respuestas->error_500("Error interno, no se ha podido guardar.");
            }

          }else{
            return $_respuestas->error_200("El $usuario esta inactivo contacte con el administrador");
          }
          
        }else{
          return $_respuestas->error_200("El password es incorrecto");
        }
        
      }else{
        # NO existe usuario
        return $_respuestas->error_200("El usuario $usuario no existe");
      }

    }
  }

  private function obtenerDatosUsuario($correo){
    $query = "SELECT UsuarioId, Password, Estado FROM usuarios WHERE Usuario = '$correo'";
    $datos = parent::obtenerDatos($query); //Como extiende llamamos a una funcion padre
    if (isset($datos[0]["UsuarioId"])) {
      return $datos;
    }else{
      return 0;
    }
  }

  /* Creamos el token y para ello utilizaremos dos funciones de php
    1. bin2hex -> devuelve un string hexadecimal.
    2. openssl_random_pseudo_bytes -> Genera una cadena de bytes aleatorias
  */
  private function insertarToken($usuarioId){
    $val = true;
    $token = bin2hex(openssl_random_pseudo_bytes(16,$val));
    //$date = date('Y m d H:i');
    $estado = "Activo";

    $query = "INSERT INTO usuarios_token (UsuarioId,Token,Estado,Fecha) VALUES ('$usuarioId','$token','$estado',NOW())";
    $verifica = parent::nonQuery($query);
    if($verifica){
      return $token;
    }else{
      return false;
    }

  }

}

?>