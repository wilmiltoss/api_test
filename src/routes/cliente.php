<?php

    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;

    $app = new \Slim\App;

    
   // GET cadena local
     $app->get("/cliente/lista",function(Request $request, Response $response){

        try{
            $list = Clientes::get();
            pr($list);
        }catch(PDOException $e){
            echo '{"error" : {"text":'.$e.getMessage().'}';
        }
       
    });

     //INSERTAR NUEVO CLIENTE CON POST
     $app->post("/cliente/add",function(Request $request, Response $response){
        $idcliente   = $request->getParam('idcliente');
        $nit = $request->getParam('nit');
        $nombre = $request->getParam('nombre');
        $telefono = $request->getParam('telefono');
        $direccion = $request->getParam('direccion');
        $dateadd = $request->getParam('dateadd');
        $usuario_id = $request->getParam('usuario_id');
        $estatus = $request->getParam('estatus');

        $existe = Clientes::get("nit = '{$nit}'");
        if(haveRows($existe)){
            $result = array("code" => 203, "msg" => "Ya existe el numero de ci del cliente", "type" => "error");
        }else{
            $_POST["idcliente"] = (int)$idcliente;
            $_POST["nit"] = $nit;
            $_POST["nombre"] = $nombre;
            $_POST["telefono"] = $telefono;
            $_POST['direccion'] = $direccion;
            $_POST["dateadd"] = $dateadd;
            $_POST['usuario_id'] = $usuario_id;
            $_POST['estatus'] = $estatus;

            $error = Clientes::save(0);

            if(is_array($error)){
                $result = array("code" => 201, "msg" => "No se pudo cargar el registro.", "type" => "error");
            }else{
                $result = array("code" => 200, "msg" => "Punto agregado correctamente", "type" => "success");
            }
        }
        echo json_encode($result);
    });


  //MODIFICAR CLIENTE CON PUT
    $app->put("/cliente/edit/{id}",function(Request $request, Response $response){
        $idcliente = $request->getAttribute("idcliente");
        $idcliente = Clientes::select($idcliente);
        try{
            $_POST['admin_id'] = $punto[0]['admin_id'];
            $_POST['local_id'] = $punto[0]['local_id'];
            $_POST["punto_nombre"] = $request->getParam('titulo');
            $_POST["punto_orden"] = $request->getParam('orden');
            $_POST["punto_ubicacion"] = $request->getParam('ubicacion');
            $_POST["punto_tiempo"] = $request->getParam('tiempo');
            $_POST["punto_status"] = $punto[0]['punto_status'];
            Local_punto::save($punto_id);
            echo json_encode("Punto modificado");
        }catch(Exception $e){
            echo '{error: {"text": '.$e->getMessage().'}}';
        }
    });




    // Prueba Define app routes
/*$app->get('/hello/{name}', function (Request $request, Response $response, $args) {
    $name = $args['name'];
    $response->getBody()->write("Hello, $name");
    return $response;
});*/