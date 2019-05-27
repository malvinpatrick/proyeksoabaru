<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use Firebase\JWT\JWT;

return function (App $app) {
    $app->add(new \Tuupola\Middleware\JwtAuthentication([
        'path' => '/',
        'attribute' => 'jwt',
        'secret' => 'sangatpanjang',
        'algorithm' => ['HS256'],
        // "after" => function ($response, $arguments) {
        //     $data['status'] = 'success';
        //     $data["username"] = $arguments["decoded"]["username"];
        // },
        'error' => function ($response, $arguments)
        {
            $data['status'] = 'error';
            $data['message'] = 'Unauthorized';
            return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->write(json_encode($data));
        }
    ]));
};
