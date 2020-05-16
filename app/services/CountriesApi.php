<?php

namespace VanillaAuth\Services;

use Exception;
use GuzzleHttp\Client;

class CountriesApi
{
    protected $baseUrl = "http://countryapi.gear.host/v1/Country/getCountries";
    protected $endpoint;
    protected $limit;
    protected $page;
    public function __construct($limit = 50)
    {
        $this->limit = $limit;
    }
    protected function setEndpoint($page)
    {
        $this->endpoint = $this->baseUrl . "?pLimit=$this->limit&pPage=$page";
    }
    public function getCountries($page = 1)
    {
        $this->setEndpoint($page);

        $client = new Client();

        $response = $client->request('GET', $this->endpoint);
        $response = json_decode($response->getBody());
        if($response->IsSuccess){
            return $response->Response;
        }
       else{
           throw new Exception("Couldn't connect to API");die;
       }
    }
}
