<?php

use FFI\ParserException;

require_once('conexion/conexion.php');
require_once('respuestas.class.php');

class pacientes extends conexion{

  private $table = "pacientes";
  private $pacienteid = "";
  private $dni = "";
  private $nombre = "";
  private $direccion = "";
  private $codigoPostal = "";
  private $genero = "";
  private $telefono = "";
  private $fechaNacimiento = "0000-00-00";
  private $correo = "";
  private $token = ""; //493047293cd1728849dc0da7574f5641 token usuario1

  public function listaPacientes($pagina){
    //Mostraremos los registros de 100 en 100
    $inicio = 0;
    $cantidad = 100;

    if ($pagina > 1) { //Paginacion
      $inicio = ($cantidad * ($pagina - 1) ) + 1;
      $cantidad = $cantidad * $pagina;
    }
    //Creamos el query
    $query = "SELECT PacienteId,Nombre,DNI,Telefono,Correo FROM " . $this->table . " limit $inicio,$cantidad";
    $datos = parent::obtenerDatos($query);
    return ($datos);
  }

  //Obtener un paciente
  public function obtenerPaciente($id){
    $query = "SELECT * FROM " . $this->table . " WHERE PacienteId = '$id' ";
    return parent::obtenerDatos($query);
  }
  
  public function post($json){
    $_respuestas = new respuestas;
    //Convertimos el json a un array
    $datos = json_decode($json, true);

    //Validamos si nos han enviado el token
    if (!isset($datos['token'])) {
      //Devolvemos error si no han enviado el token
      return $_respuestas->error_401();
    }else{ 
      $this->token = $datos['token'];
      $arrayToken = $this->buscarToken();
      if ($arrayToken) {
        //Validamos si nos envian los campos requeridos para hacer un post en la BBDD
        if (!isset($datos['nombre']) || !isset($datos['dni']) || !isset($datos['correo'])) {
          //Devolvemos error si falta algun campo
          return $_respuestas->error_400();
        }else{ //si estan correcto todos los campos
          $this->nombre = $datos['nombre'];
          $this->dni = $datos['dni'];
          $this->correo = $datos['correo'];
          $this->token = $datos['token'];
          //if(isset($datos["telefono"])) {$this->telefono = $datos["telefono"];}
          $this->telefono = (isset($datos["telefono"])) ? $this->telefono = $datos["telefono"] : $this->telefono = "---"; //operador ternario
          if(isset($datos["direccion"])) {$this->direccion = $datos["direccion"];}
          if(isset($datos["codigoPostal"])) {$this->codigoPostal = $datos["codigoPostal"];}
          if(isset($datos["genero"])) {$this->genero = $datos["genero"];}
          if(isset($datos["fechaNacimiento"])) {$this->fechaNacimiento = $datos["fechaNacimiento"];}
          //Insertamos el paciente
          $resp = $this->insertarPaciente();
          if($resp){
            $respuesta = $_respuestas->response;
            $respuesta["result"] = array(
              "pacienteId" => $resp
            );
            return $respuesta;
          }else{
            return $_respuestas->error_500();
          }
        }
      }else{
        return $_respuestas->error_401("El Token que envio es invalido o ha caducado");
      }
    }

  }

  //Insertar paciente POST
  private function insertarPaciente(){
    $query = "INSERT INTO " . $this->table . " (DNI,Nombre,Direccion,CodigoPostal,Telefono,Genero,FechaNacimiento,Correo)
        values
        ('" . $this->dni . "','" . $this->nombre . "','" . $this->direccion ."','" . $this->codigoPostal . "','"  . $this->telefono . "','" . $this->genero . "','" . $this->fechaNacimiento . "','" . $this->correo . "')";
    $resp = parent::nonQueryId($query);
    if ($resp) {
      return $resp;
    }else{
      return 0;
    }
  }

  public function put($json){
    $_respuestas = new respuestas;
    //Convertimos el json a un array
    $datos = json_decode($json, true);

    //Validamos si nos han enviado el token
    if (!isset($datos['token'])) {
      //Devolvemos error si no han enviado el token
      return $_respuestas->error_401();
    }else{ 
      $this->token = $datos['token'];
      $arrayToken = $this->buscarToken();
      if ($arrayToken) {
        //Validamos si nos envian los campos requeridos para hacer un post en la BBDD
        if (!isset($datos['pacienteId'])) {
          //Devolvemos error si falta algun campo
          return $_respuestas->error_400();
        }else{ //si estan correcto todos los campos
          $this->pacienteid = $datos['pacienteId'];
          if(isset($datos["nombre"])) {$this->nombre = $datos["nombre"];}
          if(isset($datos["dni"])) {$this->dni = $datos["dni"];}
          if(isset($datos["correo"])) {$this->correo = $datos["correo"];}
          //if(isset($datos["telefono"])) {$this->telefono = $datos["telefono"];}
          $this->telefono = (isset($datos["telefono"])) ? $this->telefono = $datos["telefono"] : $this->telefono = "---"; //operador ternario
          if(isset($datos["direccion"])) {$this->direccion = $datos["direccion"];}
          if(isset($datos["codigoPostal"])) {$this->codigoPostal = $datos["codigoPostal"];}
          if(isset($datos["genero"])) {$this->genero = $datos["genero"];}
          if(isset($datos["fechaNacimiento"])) {$this->fechaNacimiento = $datos["fechaNacimiento"];}
    
          //Insertamos el paciente
          $resp = $this->modificarPaciente();
          if($resp){
            $respuesta = $_respuestas->response;
            $respuesta["result"] = array(
              "pacienteIdModificado" => $this->pacienteid
            );
            return $respuesta;
          }else{
            return $_respuestas->error_500();
          } 
        }
      }else{
        return $_respuestas->error_401("El Token que envio es invalido o ha caducado");
      }
    }


  }

  //Modificar paciente PUT
  private function modificarPaciente(){
    $query = "UPDATE " . $this->table . " SET Nombre ='" . $this->nombre . "',Direccion = '" . $this->direccion . "', DNI = '" . $this->dni . "', CodigoPostal = '" .
    $this->codigoPostal . "', Telefono = '" . $this->telefono . "', Genero = '" . $this->genero . "', FechaNacimiento = '" . $this->fechaNacimiento . "', Correo = '" . $this->correo .
    "' WHERE PacienteId = '" . $this->pacienteid . "'"; 
    
    
    $resp = parent::nonQuery($query);
    if ($resp >= 1) {
      return $resp;
    }else{
      return 0;
    }
  }
  
  //Borrar paciente DELETE
  public function delete($json){
    $_respuestas = new respuestas;
    //Convertimos el json a un array
    $datos = json_decode($json, true);

    //Validamos si nos han enviado el token
    if (!isset($datos['token'])) {
      //Devolvemos error si no han enviado el token
      return $_respuestas->error_401();
    }else{ 
      $this->token = $datos['token'];
      $arrayToken = $this->buscarToken();
      if ($arrayToken) {
        //Validamos si nos envian los campos requeridos para hacer un post en la BBDD
        if (!isset($datos['pacienteId'])) {
          //Devolvemos error si falta algun campo
          return $_respuestas->error_400();
        }else{ //si estan correcto todos los campos
          $this->pacienteid = $datos['pacienteId'];
    
          //Borrar el paciente
          $resp = $this->borrarPaciente();
          if($resp){
            $respuesta = $_respuestas->response;
            $respuesta["result"] = array(
              "pacienteIdBorrado" => $this->pacienteid
            );
            return $respuesta;
          }else{
            return $_respuestas->error_500();
          } 
        }
      }else{
        return $_respuestas->error_401("El Token que envio es invalido o ha caducado");
      }
    }


  }

  private function borrarPaciente(){
    $query = "DELETE FROM " . $this->table . " WHERE PacienteId= '" . $this->pacienteid . "'";
    $resp = parent::nonQuery($query);
    if($resp >= 1 ){
        return $resp;
    }else{
        return 0;
    }
  }

  //Comprobamos la existencia del token y que este activo
  private function buscarToken(){
    $query = "SELECT TokenId,UsuarioId,Estado FROM usuarios_token WHERE Token = '" . $this->token . "' AND Estado = 'Activo' ";
    $resp = parent::obtenerDatos($query);
    if($resp){
      return $resp;
    }else{
      return 0;
    }
  }

  private function actualizarToken($tokenid){
    $date = date("Y m d H:i");
    $query = "UPDATE usuarios_token SET Fecha = NOW() WHERE TokenId = '$tokenid";
    $resp = parent::nonQuery($query);
    if ($resp >= 1) {
      return $resp;
    }else{
      return 0;
    }
  }
  
}

?>