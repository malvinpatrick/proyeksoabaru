<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use Firebase\JWT\JWT;
date_default_timezone_set("Asia/Jakarta");
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
                        'url_site' => $value->url
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
        $username = $request->getAttribute('jwt')['username'];
        $sql=$this->db->prepare("SET time_zone='+07:00';");
        $sql->execute();

        $tempSQL = "SELECT username, limit_access,(SELECT COUNT(id) FROM log_access WHERE log_access.username = user.username AND DATE_FORMAT(accessAt, '%d-%m-%Y') = DATE_FORMAT(NOW(), '%d-%m-%Y')) AS count_access FROM user WHERE username = '" . $username ."'";
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
                foreach ($response as $key => $value) {
                    $product[] = array(
                        'name' => $value->nama_barang,
                        'price' => $value->harga,
                        'image_url' => $value->image_url,
                        'url_site' => $value->url
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
            header("Content-Type: application/json");
            echo json_encode(array(
                'error' => true, 
                'message' => 'user not valid'
            ));
        }        
    });
    $app->get('/search', function (Request $request, Response $response, array $args) {
        $username = $request->getAttribute('jwt')['username'];
        $sql=$this->db->prepare("SET time_zone='+07:00';");
        $sql->execute();

        $tempSQL = "SELECT username, limit_access,(SELECT COUNT(id) FROM log_access WHERE log_access.username = user.username AND DATE_FORMAT(accessAt, '%d-%m-%Y') = DATE_FORMAT(NOW(), '%d-%m-%Y')) AS count_access FROM user WHERE username = '" . $username ."'";
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
                //insert database
                $sql=$this->db->prepare("INSERT INTO log_access VALUES(null, '$username',now(),'$product_name')");
                $sql->execute();
                return $this->response->withJson($retail);
            }else{
                return $this->response->withJson(array(
                    'status' => 'failed',
                    'message' => 'You have reach limit access!!'
                ));
            }
            
        }else{
            header("Content-Type: application/json");
            echo json_encode(array(
                'error' => true, 
                'message' => 'user not valid'
            ));
        }
    });
    $app->get('/favorite', function (Request $request, Response $response, array $args) {
        $username = $request->getAttribute('jwt')['username'];
        $sql=$this->db->prepare("SET time_zone='+07:00';");
        $sql->execute();

        $tempSQL = "SELECT username, limit_access,(SELECT COUNT(id) FROM log_access WHERE log_access.username = user.username AND DATE_FORMAT(accessAt, '%d-%m-%Y') = DATE_FORMAT(NOW(), '%d-%m-%Y')) AS count_access FROM user WHERE username = '" . $username ."'";
        $sql=$this->db->prepare($tempSQL);
        $sql->execute();
        $user = $sql->fetchObject();
        if($user){
            if($user->limit_access > 0){
                $tempSQL = "select keyword,COUNT(keyword) as count from log_access GROUP BY keyword ORDER BY COUNT(keyword) DESC,keyword";
                $sql=$this->db->prepare($tempSQL);
                $sql->execute();
                $retail = $sql->fetchAll();
                //insert database
                $sql=$this->db->prepare("INSERT INTO log_access VALUES(null, '$username',now(),'$product_name')");
                $sql->execute();
                return $this->response->withJson($retail);
            }else{
                return $this->response->withJson(array(
                    'status' => 'failed',
                    'message' => 'You have reach limit access!!'
                ));
            }
        }else{
            header("Content-Type: application/json");
            echo json_encode(array(
                'error' => true, 
                'message' => 'user not valid'
            ));
        }
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
                //insert database
                $sql=$this->db->prepare("INSERT INTO log_access VALUES(null, '$username',now(),'$product_name')");
                $sql->execute();
                return $this->response->withJson($retail);
            }else{
                return $this->response->withJson(array(
                    'status' => 'failed',
                    'message' => 'You have reach limit access!!'
                ));
            }
        }else{
            header("Content-Type: application/json");
            echo json_encode(array(
                'error' => true, 
                'message' => 'user not valid'
            ));
        }
    });

    $app->get('/topup', function (Request $request, Response $response, array $args) {
        $username = $request->getAttribute('jwt')['username'];
        if($request->getQueryParam('mode')){
            if($request->getQueryParam('mode') == 'C') $limit = 75;
            else if($request->getQueryParam('mode') == 'B') $limit = 100;
            else if($request->getQueryParam('mode') == 'A') $limit = 200;
            else{
                return $this->response->withJson(array(
                    'status' => 'error',
                    'message' => "Mode isn't valid!"
                ), 410);
            }
        }

        $now_seconds = time();
        $payload = array(
            'request_limit' => $limit,
            "iat" => $now_seconds,  
            "exp" => $now_seconds+(60*30)
        );
        return $this->response->withJson(array(
            'token' => JWT::encode($payload, 'sangatpanjang', "HS256")
        ));
    });

    $app->post('/topup/submit', function (Request $request, Response $response, array $args) {
        $username = $request->getAttribute('jwt')['username'];
        $input = $request->getParsedBody();
        try{
            $decoded = JWT::decode($input['token'], 'sangatpanjang', array('HS256'));
            $tempSQL = "UPDATE user SET limit_access = $decoded->request_limit  WHERE username = '$username'";
            $sql=$this->db->prepare($tempSQL);
            $sql->execute();

            $filenya = $request->getUploadedFiles();
            $file = $filenya['photo'];
            if($file->getError() == UPLOAD_ERR_OK){
                $ekstension = pathinfo($file->getClientFilename(), PATHINFO_EXTENSION);
                $filename = sprintf('%s.%0.8s', time(), $ekstension);
                $directory = $this->get('settings')['upload_directory'];
                $file->moveTo($directory . DIRECTORY_SEPARATOR . $filename);
                return $response->withJson([
                    'status' => 'success',
                    'limit_access' => $decoded->request_limit
                ], 200);
            }
            return $this->response->withJson($decoded->request_limit);
        }catch(\Exception $e){
            if($e->getMessage() == 'Expired token'){
                return $this->response->withJson(array(
                    'status' => 'error',
                    'message' => 'Expired token!'
                ));
            }
        }
    });
};


