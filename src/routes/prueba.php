<?php

    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;

    $app = new \Slim\App;


        // GET cadena local
    $app->get("/locales/list",function(Request $request, Response $response){
        $list = Locales::get();
        pr($list);
    });

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
                    "msg" => "404 - No se encontraron resultados usuarios"
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