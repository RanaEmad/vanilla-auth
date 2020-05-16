<?php
namespace VanillaAuth\Controllers;

use VanillaAuth\Core\Loader;
use VanillaAuth\Core\Request;
use VanillaAuth\Services\CountriesApi;

class CountryController{
    public function index(){
      $api= new CountriesApi(4);
      $page=Request::get("page");
      $previous="";
      $next="";
      if($page){
        if($page>1){
            $previous=$page-1;
        }
        $next= $page+1;
      }
      $countries=$api->getCountries($page);
        Loader::view("countries/index",compact(["countries","previous","next"]));
    }
}