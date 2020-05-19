<?php

namespace VanillaAuth\Traits;

use VanillaAuth\Factories\UserFactory;

trait GuzzleAuthTrait
{
    protected function logUserIn()
    {
        $uri = $this->baseUrl . "/users/auth/login";

        $user = UserFactory::create();
        $this->client->request('POST', $uri, [
            'form_params' => [
                'email' => $user["email"],
                'password' => $user["plainPassword"],
                'csrf' => $this->getCsrf("/users/auth/login")
            ],
            'cookies' => $this->jar
        ]);
        return $user;
    }
    protected function getCsrf($uri)
    {
        $uri = $this->baseUrl . $uri;

        $response = $this->client->request('GET', $uri, ['cookies' => $this->jar]);
        $content = $response->getBody()->getContents();
        $pattern = '#<input type="hidden" name="csrf" value="(.*?)"#si';
        preg_match($pattern, $content, $matches);
        $token = $matches[1];
        return $token;
    }
}
