<?php

    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;

    $app = new \Slim\App;


        // GET cadena local
    $app->get("/cliente/lista",function(Request $request, Response $response){
        $list = Clientes::get();
        pr($list);
    });


    // Prueba Define app routes
/*$app->get('/hello/{name}', function (Request $request, Response $response, $args) {
    $name = $args['name'];
    $response->getBody()->write("Hello, $name");
    return $response;
});*/