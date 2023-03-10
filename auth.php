
<?php

require_once './clases/auth.class.php';
require_once './clases/respuestas.class.php';

//Al profesor le gusta instanciar las clases con $
$_auth = new auth;
$_respuestas = new respuestas;

/* Comprobamos como nos llega la informacion por POST o por otro método,
si es por POST leemos el contenido que nos llega */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  //Recibir datos
  $postBody = file_get_contents("php://input"); //Leemos lo que nos llega por POST
  //Enviamos datos al manejador
  $datosArray = $_auth->login($postBody); //Llamamos a la funcion login para comprobacion
  //devolvemos la respuesta
  header('Content-Type: application/json');
  if(isset($datosArray['result']['error_id'])){
    $responseCode = $datosArray['result']['error_id'];
    http_response_code($responseCode);
    
  }else{
    http_response_code(200);
  }
  echo(json_encode($datosArray));
  
}else{
  header('Content-Type: application/json');
  $datosArray = $_respuestas->error_405();
  echo(json_encode($datosArray));
}
?>