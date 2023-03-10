<?php

require_once 'conexion/conexion.php';

class token extends conexion{

  public function actualizarToken($fecha){
    //Inactivara todos los Tokens que esten Activos con fecha anterior a la que se le envia
    $query = "UPDATE usuarios_token SET Estado = 'Inactivo' WHERE Fecha < '$fecha' AND Estado = 'Activo'";
    $verificar = parent::nonQuery($query);
    if ($verificar > 0) {
      return 1;
    }else{
      return 0;
    }
  }

}

?>