<?php

    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;

    $app = new \Slim\App;

    // GET Todos los usuarios
    $app->get("/admin/list",function(Request $request, Response $response){
        $list = Admins::get();
        pr($list);
    });
    
    //GET por usuario
    $app->get("/admin/{id}",function(Request $request, Response $response){
        try {
            $admin_id = $request->getAttribute('id');
            $admin = Admins::select($admin_id);
            if(haveRows($admin)){
                pr($admin);
            }else{
                $return = array(
                    "code" => 404,
                    "msg" => "404 - No se encontraron resultados"
                );
                echo json_encode($return);
            }
        } catch (Exception $e) {
            echo '{error: {"text": '.$e->getMessage().'}}';
        }
    });

    $app->post("/admin/login",function(Request $request, Response $response){
        $user = $request->getParam('user');
        $pass = $request->getParam('password');
        
        try {
            $login = Login::set(strtolower($user), strtolower($pass));

            if(!$login):
                if(haveRows($admin_status)):
                    $failed_attempts = "SELECT * FROM admin_login_attempts WHERE admin_id = {$admin_status[0]['admin_id']} AND admin_login_response = 'FAILED' AND UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(admin_login_timestamp) < 600";
                    $failed_attempts = db::execute($failed_attempts);
                    
                    if(count($failed_attempts) > 5):
                        Admins::set("admin_status",0,"admin_id='{$admin_status[0]['admin_id']}'");
                        $return = array(
                            "code" => 403,
                            "msg" => "USUARIO BLOQUEADO: Se detectaron 6 intentos fallidos de ingreso."
                        );
                    endif;
                endif;
            
                $_SESSION['_lgatt'] = (int)$_SESSION['_lgatt'] + 1;
                $return = array(
                    "code" => 404,
                    "msg" => "Datos incorrectos"
                );
            else:
                 if(!access("local_marcacion")){
                     return json_encode(array("code" => 201, "text" => "Usuario no autorizado"));
                     exit;
                 }

                $local_id = Login::get("local_id");
                $local = Cadena_local::select($local_id);

                if(in_array($local_id, granUnion)){
                    $local_id = 13;
                }

                $local = Cadena_local::get("local_codigo = '{$local_id}'");
                $local = Cadena_local::select($local_id);
                $nombre = ucwords(strtolower(Login::get("admin_names")));

                $return = array(
                    "code" => 200,
                    "token" => authToken,
                    "admin_id" => Login::get("admin_id"),
                    "nombre" => $nombre,
                    "agregaPunto" => access("local_punto"),
                    "local_id" => number_format($local_id,1,'.',''),
                    "local_nombre" => $local[0]['local_nombre'],
                    "rol_id" => login::get("rol_id"),
                    "totalPuntos" => (int)Local_marcacion::total($local_id)
                );
            endif;

            echo json_encode($return);
            
        } catch (Exception $e) {
            echo '{error: {"text": '.$e->getMessage().'}';
        }   
    });


/*********************************************/
/*********************************************/
/*            puntos de marcacion            */
/*********************************************/
/*********************************************/

    $app->get("/point/{id}",function(Request $request, Response $response){
        try {
            $local_id = $request->getAttribute('id');
            $local = Cadena_local::select($local_id);        
            $puntos = Local_punto::get(null, "punto_orden ASC");
            if(haveRows($puntos)){
                echo json_encode($puntos);
            }else{
                $return = array(
                    "code" => 404,
                    "msg" => "No se encontraron resultados"
                );
                echo json_encode($return);
            }
        } catch (Exception $e) {
            echo '{error: {"text": '.$e->getMessage().'}}';
        }
    }); 

    $app->put("/point/edit/{id}",function(Request $request, Response $response){
        $punto_id = $request->getAttribute("id");
        $punto = Local_punto::select($punto_id);
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

    $app->post("/point/add",function(Request $request, Response $response){
        $admin_id = $request->getParam('admin_id');
        $admin = Admins::select($admin_id);

        $local_id = $admin[0]['local_id'];
        $local = Cadena_local::select($local_id);
        if(in_array($local[0]['local_codigo'], granUnion)){
            $local_id = 13;
        }

        $orden = $request->getParam('orden');
        $existe = Local_punto::get("local_id = '{$local_id}' AND punto_orden = '{$orden}'");
        if(haveRows($existe)){
            $result = array("code" => 203, "msg" => "Ya existe el punto $orden en tu local.", "type" => "error");
        }else{
            $_POST["admin_id"] = (int)$admin_id;
            $_POST["local_id"] = $local_id;
            $_POST["punto_nombre"] = $request->getParam('titulo');
            $_POST["punto_orden"] = $orden;
            $_POST['punto_ubicacion'] = $request->getParam('ubicacion');
            $_POST['punto_tiempo'] = convertir($request->getParam('tiempo'));
            $_POST['punto_status'] = 1;
            $_POST['punto_latlong'] = $request->getParam('latLong');
            $error = Local_punto::save(0);

            if(is_array($error)){
                $result = array("code" => 201, "msg" => "No se pudo cargar el registro.", "type" => "error");
            }else{
                $result = array("code" => 200, "msg" => "Punto agregado correctamente", "type" => "success");
            }
        }
        echo json_encode($result);
    });

    $app->delete("/point/delete/{id}",function(Request $request, Response $response){
        $punto_id = $request->getAttribute('id');
        Local_punto::delete($punto_id);
        echo json_encode("punto Eliminado");
        exit;
    });

    $app->post("/point/guardamarca",function(Request $request, Response $response){
        $_POST['local_id'] =  (int)$request->getParam('local_id');
        $_POST['admin_id'] =  $request->getParam('admin_id');
        $_POST['punto_orden'] =  $request->getParam('orden');
        $_POST['marca_ubicacion'] =  $request->getParam('lat').','.$request->getParam('long');
        $_POST['marca_observacion'] =  '';
        $_POST['marca_url'] =  $request->getParam('url');
        $_POST['marca_status'] = 1;
        $error = Local_marcacion::save(0);

        if(is_array($error)){
            $result = array("code" => 201, "msg" => "No se pudo cargar el registro.");
        }else{
            $result = array("code" => 200);
        }
        echo json_encode($result);
        exit;
    });

        // GET cadena local
    $app->get("/cadena/list",function(Request $request, Response $response){
        $list = Cadena_local::get();
        pr($list);
    });
    