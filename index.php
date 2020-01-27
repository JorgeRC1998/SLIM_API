<?php

header('Access-Control-Allow-Origin: *');

require './vendor/autoload.php';

$app = new \Slim\Slim();

//Retorna todos los registros de la tabla
$app->get('/select', function () {
    $enlace = mysqli_connect("127.0.0.1", "root", "", "db_final");
    if (!$enlace) {
    echo "Error: No se pudo conectar a MySQL." . PHP_EOL;
    echo "errno de depuración: " . mysqli_connect_errno() . PHP_EOL;
    echo "error de depuración: " . mysqli_connect_error() . PHP_EOL;
    exit;
    }

    // Sentencia SQL: muestra todo el contenido de la tabla "books" 
    $sentencia = "SELECT * FROM tbl_movimientos"; 
    // Ejecuta la sentencia SQL 
    $resultado = mysqli_query($enlace , $sentencia); 
    if(!$resultado) 
    die("Error: no se pudo realizar la consulta");
    
    $array = [];
    while($fila = mysqli_fetch_assoc($resultado)) 
    { 

    $miArray = array("ID"=>$fila['ID'],
                     "TIPO"=>$fila['TIPO'],
                     "CONCEPTO"=>$fila['CONCEPTO'], 
                     "VALOR"=>$fila['VALOR'], 
                     "OBSERVACION"=>$fila['OBSERVACION'], 
                     "RECURRENTE"=>$fila['RECURRENTE'],
                     "FECHA_REGISTRO"=>$fila['FECHA_REGISTRO'],
                     "EVIDENCIA"=>$fila['EVIDENCIA']);
    array_push($array , $miArray);
    } 
    echo(json_encode(array("datos" => $array)));

    // Libera la memoria del resultado
    mysqli_free_result($resultado);

    // Cierra la conexión con la base de datos 
    mysqli_close($enlace); 
});

//Inserta un nuevo movimiento
$app->post('/insertar', function () use ($app){
    
        //Request recoge variables de las peticiones http
        $request = $app->request;
        $db = new mysqli('127.0.0.1', 'root', '', 'db_final');


        $insert = $db->query("INSERT INTO tbl_movimientos (TIPO, CONCEPTO, VALOR, OBSERVACION, RECURRENTE, FECHA_REGISTRO, EVIDENCIA) VALUES(
                                               '{$request->post("TIPO")}',
                                               '{$request->post("CONCEPTO")}',
                                               '{$request->post("VALOR")}',
                                               '{$request->post("OBSERVACION")}',
                                               '{$request->post("RECURRENTE")}',
                                               '{$request->post("FECHA_REGISTRO")}',
                                               '{$request->post("EVIDENCIA")}'
                                               )");
        if ($insert) {
            $result = array("status" => "true", "message" => "Registro creado correctamente");
        } else {
            $result = array("status" => "false", "message" => "Registro NO creado");
        }
        echo json_encode($result);
        
});

//Elimina un registro mediante un ID
$app->delete('/eliminar/:id', function ($ID) use ($app){
    $enlace = mysqli_connect("127.0.0.1", "root", "", "db_final");
    if (!$enlace) {
    echo "Error: No se pudo conectar a MySQL." . PHP_EOL;
    echo "errno de depuración: " . mysqli_connect_errno() . PHP_EOL;
    echo "error de depuración: " . mysqli_connect_error() . PHP_EOL;
    exit;
    }

   // Selecciona la base de datos 
   if(!mysqli_select_db($enlace , "db_final")) 
    die("Error: No existe la base de datos");

    $sentencia = "DELETE FROM tbl_movimientos WHERE ID='$ID'";    
    
    $resultado = mysqli_query($enlace , $sentencia); 
   
    if($resultado){
        echo "ELIMINACION realizada";
    }else{
        echo "Algo fue mal";
    }
});

//Elimina varios registros mediante un CONCEPTO de movimiento
$app->delete('/eliminarCon/:con', function ($CONCEPTO) use ($app){
    $enlace = mysqli_connect("127.0.0.1", "root", "", "db_final");
    if (!$enlace) {
    echo "Error: No se pudo conectar a MySQL." . PHP_EOL;
    echo "errno de depuración: " . mysqli_connect_errno() . PHP_EOL;
    echo "error de depuración: " . mysqli_connect_error() . PHP_EOL;
    exit;
    }

   // Selecciona la base de datos 
   if(!mysqli_select_db($enlace , "db_final")) 
    die("Error: No existe la base de datos");

    $sentencia = "DELETE FROM tbl_movimientos WHERE CONCEPTO='$CONCEPTO'";    
    
    $resultado = mysqli_query($enlace , $sentencia); 
   
    if($resultado){
        echo "ELIMINACION realizada";
    }else{
        echo "Algo fue mal";
    }
});

//Actualiza un movimiento mediante un ID
$app->put('/actualizar/:id', function ($ID) use ($app) {
  //Request recoge variables de las peticiones http
  $request = $app->request;
  $db = new mysqli('127.0.0.1', 'root', '', 'db_final');


  $upt = $db->query("UPDATE tbl_movimientos SET VALOR = '{$request->post("VALOR")}', OBSERVACION = '{$request->post("OBSERVACION")}' WHERE ID = '$ID'");
  if ($upt) {
      $result = array("status" => "true", "message" => "Registro actualizado correctamente");
  } else {
      $result = array("status" => "false", "message" => "Registro NO actualizado");
  }
  echo json_encode($result);
});

//Actualiza varios movimientos segun un CONCEPTO
$app->put('/actualizarCon/:id', function ($CONCEPTO) use ($app) {
    //Request recoge variables de las peticiones http
    $request = $app->request;
    $db = new mysqli('127.0.0.1', 'root', '', 'db_final');
  
  
    $upt = $db->query("UPDATE tbl_movimientos SET VALOR = '{$request->post("VALOR")}', OBSERVACION = '{$request->post("OBSERVACION")}' WHERE CONCEPTO = '$CONCEPTO'");
    if ($upt) {
        $result = array("status" => "true", "message" => "Registro\s actualizado correctamente");
    } else {
        $result = array("status" => "false", "message" => "Registro NO actualizado");
    }
    echo json_encode($result);
  });
   
$app->run();

?>