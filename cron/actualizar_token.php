<?php

  require_once '../clases/token.class.php';

  //Instanciamos la clase
  $_token = new token;
  //Creamos una fecha, la de hoy
  $fecha = date('Y-m-d');

  //Ejecutamos el metodo con la fecha de hoy
  echo $_token->actualizarToken($fecha);

?>