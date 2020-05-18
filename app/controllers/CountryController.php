<?php

namespace VanillaAuth\Controllers;

use VanillaAuth\Core\Loader;
use VanillaAuth\Core\Request;
use VanillaAuth\Core\Session;
use VanillaAuth\Services\CountriesApi;

class CountryController
{
  public function index()
  {
    Session::checkLogin();
    $api = new CountriesApi(4);
    $page = Request::get("page");
    $previous = "";
    $next = "";
    if (!$page) {
      $page = 1;
    }
    if ($page > 1) {
      $previous = $page - 1;
    }
    $next = $page + 1;
    $countries = $api->getCountries($page);
    Loader::view("countries/index", compact(["countries", "previous", "next"]));
  }
}
