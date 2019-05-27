<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

return function (App $app) {    
    $app->get('/tokopedia', function (Request $request, Response $response, array $args) {
        $username = $request->getAttribute('jwt')['username'];
        $sql=$this->db->prepare("SET time_zone='+07:00';");
        $sql->execute();

        $tempSQL = "SELECT username, limit_access,(SELECT COUNT(id) FROM log_access WHERE log_access.username = user.username AND DATE_FORMAT(accessAt, '%d-%m-%Y') = DATE_FORMAT(NOW(), '%d-%m-%Y')) AS count_access FROM user WHERE username = '" . $username ."'";
        $sql=$this->db->prepare($tempSQL);
        $sql->execute();
        $user = $sql->fetchObject();
        if($user){
            if((int)$user->count_access <= (int)$user->limit_access){
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
                //insert database
                $sql=$this->db->prepare("INSERT INTO log_access VALUES(null, '$username',now(),'$product_name')");
                $sql->execute();
                return $this->response->withJson($product);
            }else{
                return $this->response->withJson(array(
                    'status' => 'failed',
                    'message' => 'You have reach limit access!!'
                ));
            }
            
        }else{

        }
        header("Content-Type: application/json");
        echo json_encode(array(
            'error' => true, 
            'message' => 'user not valid'
        ));
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


