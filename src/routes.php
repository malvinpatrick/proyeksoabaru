<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

return function (App $app) {
    $container = $app->getContainer();

    function checkLimitUser($username)
    {
        
    }
    
    $app->get('/tokopedia', function (Request $request, Response $response, array $args) {
        $product_name = $request->getQueryParam('q');
        $product_name = str_replace(" ", "+", $product_name);

        $url = "https://ace.tokopedia.com/search/product/v3?scheme=https&related=true&device=desktop&catalog_rows=100&source=search&ob=23&st=product&rows=100&q=" . $product_name;

        if($request->getQueryParam('minprice')){
            $url .= "&pmin=" .  $request->getQueryParam('minprice');
        }

        if($request->getQueryParam('maxprice')){
            $url .= "&pmax=" .  $request->getQueryParam('maxprice');
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        
        $response = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($response);

        $product = [];
        foreach ($response->data->products as $key => $value) {
            $product[] = array(
                'name' => $value->name,
                'price' => $value->price,
                'image_url' => $value->image_url,
                'url_site' => $value->url,
                'location' => $value->shop->city
            );
        }

        return $this->response->withJson($request->getAttribute("jwt"));
    });

    $app->get('/lazada', function (Request $request, Response $response, array $args) {
        $product_name = $request->getQueryParam('q');
        $product_name = str_replace(" ", "+", $product_name);

        $command = escapeshellcmd("python lazada.py  $product_name");
        $output = shell_exec("python lazada.py  $product_name");
        $data = json_decode($output, true);

        // $product = [];
        // foreach ($data as $key => $value) {
        //     $product[] = array(
        //         'name' => $value->name,
        //         'price' => $value->price,
        //         'image_url' => $value->image_url,
        //         'url_site' => $value->url
        //     );
        // }

        header("Content-Type: application/json");
        echo json_encode($output);
    });
};


