<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

return function (App $app) {    
    $app->get('/tokopedia', function (Request $request, Response $response, array $args) {
        $username = $request->getAttribute('jwt')['username'];
        $tempSQL = "select * from user where username='" . $username ."'";
        $sql=$this->db->prepare($tempSQL);
        $sql->execute();
        $user = $sql->fetchObject();
        if($user){
            if($user->limit_access > 0){
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
            
        }else{

        }
        header("Content-Type: application/json");
        echo json_encode(array(
            'error' => true, 
            'message' => 'user not valid'
        ));
    });

    $app->get('/lazada', function (Request $request, Response $response, array $args) {
        $username = $request->getAttribute('jwt')['username'];
        $tempSQL = "select * from user where username='" . $username ."'";
        $sql=$this->db->prepare($tempSQL);
        $sql->execute();
        $user = $sql->fetchObject();
        if($user){
            if($user->limit_access > 0){
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
            
        }else{

        }
        header("Content-Type: application/json");
        echo json_encode(array(
            'error' => true, 
            'message' => 'user not valid'
        ));
    });
    $app->get('/search', function (Request $request, Response $response, array $args) {
        $username = $request->getAttribute('jwt')['username'];
        $tempSQL = "select * from user where username='" . $username ."'";
        $sql=$this->db->prepare($tempSQL);
        $sql->execute();
        $user = $sql->fetchObject();
        if($user){
            if($user->limit_access > 0){
                $product_name = $request->getQueryParam('q');
                $product_name = str_replace(" ", "+", $product_name);

                $url = "https://ace.tokopedia.com/search/v1/shop?scheme=https&device=desktop&related=true&st=shop&rows=60&q=" . $product_name;
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                
                $response = curl_exec($ch);
                curl_close($ch);

                $response = json_decode($response);

                $retail = [];
                foreach ($response->data as $key => $value) {
                    $retail[] = array(
                        'name' => $value->name,
                        'desc' => $value->desc,
                        'uri' => $value->uri,
                        'image_uri' => $value->image_uri
                    );
                }

                return $this->response->withJson($retail);
            }
            
        }else{

        }
        header("Content-Type: application/json");
        echo json_encode(array(
            'error' => true, 
            'message' => 'user not valid'
        ));
    });
    $app->get('/favorite', function (Request $request, Response $response, array $args) {
        $username = $request->getAttribute('jwt')['username'];
        $tempSQL = "select * from user where username='" . $username ."'";
        $sql=$this->db->prepare($tempSQL);
        $sql->execute();
        $user = $sql->fetchObject();
        if($user){
            if($user->limit_access > 0){
                $tempSQL = "select keyword,COUNT(keyword) as count from log_access GROUP BY keyword ORDER BY COUNT(keyword) DESC,keyword";
                $sql=$this->db->prepare($tempSQL);
                $sql->execute();
                $retail = $sql->fetchAll();
                return $this->response->withJson($retail);
            }
            
        }else{

        }
        header("Content-Type: application/json");
        echo json_encode(array(
            'error' => true, 
            'message' => 'user not valid'
        ));
    });
    $app->get('/myfavorite', function (Request $request, Response $response, array $args) {
        $username = $request->getAttribute('jwt')['username'];
        $tempSQL = "select * from user where username='" . $username ."'";
        $sql=$this->db->prepare($tempSQL);
        $sql->execute();
        $user = $sql->fetchObject();
        if($user){
            if($user->limit_access > 0){
                $tempSQL = "select keyword,COUNT(keyword) as count from log_access where username = '".$username."' GROUP BY keyword ORDER BY COUNT(keyword) DESC,keyword";
                $sql=$this->db->prepare($tempSQL);
                $sql->execute();
                $retail = $sql->fetchAll();
                return $this->response->withJson($retail);
            }
            
        }else{

        }
        header("Content-Type: application/json");
        echo json_encode(array(
            'error' => true, 
            'message' => 'user not valid'
        ));
    });
};


