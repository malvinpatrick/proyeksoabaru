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
        $validLimit = true;
        if($validLimit == "true"){
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

            return $this->response->withJson($product);
        }
    });

    $app->get('/lazada', function (Request $request, Response $response, array $args) {
        $validLimit = checkLimitUser();
        if($validLimit == "true"){
            $product_name = $request->getQueryParam('q');
            $product_name = str_replace(" ", "+", $product_name);

            $url = "http://camiluan.com:85/tokopedia?q=" . $product_name;

            if($request->getQueryParam('minprice')){
                $url .= "&minprice=" .  $request->getQueryParam('minprice');
            }

            if($request->getQueryParam('maxprice')){
                $url .= "&maxprice=" .  $request->getQueryParam('maxprice');
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

            return $this->response->withJson($product);
        }
    });
};


