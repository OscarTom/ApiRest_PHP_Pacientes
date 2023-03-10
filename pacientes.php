<?php

include_once('clases/respuestas.class.php');
include_once('clases/pacientes.class.php');

// Instanciamos las clases
$_respuestas = new respuestas;
$_pacientes = new pacientes;


//Comprobamos por que metodo nos vienen las consultas GET POST PUT DELETE
/***************************************************************GET */
if ($_SERVER['REQUEST_METHOD'] == "GET") {
  //echo 'Hola GET';
  if (isset($_GET["page"])) { //si especifican la pagina en el navegador ?page=
    $pagina = $_GET["page"];
    $listaPacientes = $_pacientes->listaPacientes($pagina);
    header("Content-Type: application/json");
    echo json_encode($listaPacientes);
    http_response_code(200);
    
  }else if(isset($_GET["id"])){ //Si especifican un paciente concreto
    $pacienteId = $_GET["id"];
    $datosPaciente = $_pacientes->obtenerPaciente($pacienteId);
    header("Content-Type: application/json");
    echo json_encode($datosPaciente);
    http_response_code(200);
    
  }else{//Si no viene nada como variable en la url, mostraremos la pagina 1
    $listaPacientes = $_pacientes->listaPacientes(1);
    header("Content-Type: application/json");
    echo json_encode($listaPacientes);
    http_response_code(200);

  }
/***************************************************************POST */
}else if($_SERVER['REQUEST_METHOD'] == "POST"){
  //Recibimos los datos
  $postBody = file_get_contents("php://input");
  //Enviamos al manejador
  $datosArray = $_pacientes->post($postBody);
  
  //devolvemos la respuesta
  header('Content-Type: application/json');
  if(isset($datosArray['result']['error_id'])){
    $responseCode = $datosArray['result']['error_id'];
    http_response_code($responseCode);
    
  }else{
    http_response_code(200);
  }
  echo(json_encode($datosArray));
  
  /***************************************************************PUT */
}else if($_SERVER['REQUEST_METHOD'] == "PUT"){
  //Recibimos los datos
  $postBody = file_get_contents("php://input");
  //Enviamos al manejador
  $datosArray = $_pacientes->put($postBody);

  //devolvemos la respuesta
  header('Content-Type: application/json');
  if(isset($datosArray['result']['error_id'])){
    $responseCode = $datosArray['result']['error_id'];
    http_response_code($responseCode);
    
  }else{
    http_response_code(200);
  }
  echo(json_encode($datosArray));
  
  /***************************************************************DELETE */
}else if($_SERVER['REQUEST_METHOD'] == "DELETE"){

  $headers = getallheaders();
  //Vamos a ver por donde nos llegan los datos si por el header o el body de la consulta
  if (isset($headers['token']) && isset($headers['pacienteId'])) {
    //Recibimos los datos por el header
    $send = [
      "token" => $headers["token"],
      "pacienteId" => $headers["pacienteId"]
    ];
    $postBody = json_encode($send); //Convertimos el array en json

  }else{
    //Recibimos los datos por el body
    $postBody = file_get_contents("php://input");
  }

  //Enviamos al manejador
  $datosArray = $_pacientes->delete($postBody);

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