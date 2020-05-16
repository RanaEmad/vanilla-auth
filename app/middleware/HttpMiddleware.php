<?php
namespace VanillaAuth\Middleware;

use GuzzleHttp\Client;
use VanillaAuth\Core\Request;

class HttpMiddleware{
    public static function handleCustomMethod($uri){
        if(array_key_exists("_method",$_REQUEST)){
            if( strtolower($_REQUEST["_method"]) =="put"){
                $client = new Client();
    
                $response=$client->request('PUT', baseUrl($uri), [
                    'form_params' => Request::post()
                ]);
                echo ($response->getBody()->getContents());die;
            }
            
        }
    }
}