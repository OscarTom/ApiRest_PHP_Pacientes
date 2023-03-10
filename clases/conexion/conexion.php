<?php

class conexion{
  private $server;
  private $user;
  private $password;
  private $database;
  private $port;
  private $conexion;

  function __construct(){
    $listaDatos = $this->datosConexion();
    foreach ($listaDatos as $key => $value) {
      $this->server = $value['server'];
      $this->user = $value['user'];
      $this->password = $value['password'];
      $this->database = $value['database'];
      $this->port = $value['port'];
   }

   $this->conexion = new mysqli($this->server, $this->user, $this->password, $this->database, $this->port);
   if($this->conexion->connect_errno){
    echo "Algo fue mal con la conexión";
    die();
   }
  }

  /* Funcion que recoge los datos de la conexion desde el archivo config */
  private function datosConexion(){
    $direccion = dirname(__FILE__); //Pillamos la ruta donde estamos
    $jsonData = file_get_contents($direccion . "/" . "config"); //Le concatenamos que archivo queremos

    return json_decode($jsonData, true); //devolvemos el json en un array
  }

  /* Funcion para prevenir fallos de escritura */
  private function convertirUTF8($array){
    array_walk_recursive($array, function(&$item, $key){
      if(!mb_detect_encoding($item, 'utf-8', true)){
        $item = utf8_encode($item);
      }
    });
    return $array;
  }

  public function obtenerDatos($sqlstr){
    $results = $this->conexion->query($sqlstr);
    $resultArray = array();
    foreach ($results as $key) {
      $resultArray[] = $key;
    }
    return $this->convertirUTF8($resultArray);
  }

  /* Nos devuelve el numero de filas afectadas por la consulta */
  public function nonQuery($sqlstr){
    $results = $this->conexion->query($sqlstr);
    return $this->conexion->affected_rows;
  }

  /* Lo utilizaremos para los INSERT, hace el INSERT y nos devuelve el ultimo id de la ultima fila insertada*/
  public function nonQueryId($sqlstr){
    $results = $this->conexion->query($sqlstr);
    $filas = $this->conexion->affected_rows;
    if($filas >= 1){
      return $this->conexion->insert_id;
    }else {
      return 0;
    }
  }
  
  /* Encriptar contraseña con md5 */
  protected function encriptar($string){
    return md5($string);
  }




}





?>