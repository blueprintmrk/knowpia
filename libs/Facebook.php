<?php

namespace tools;

require_once 'Facebook/autoload.php';

class Facebook
{
    public static function login($token)
    {
        $fb = new \Facebook\Facebook(['app_id' => "563786130802053", 'app_secret' => "76fde47e42a5a1438e03cd536cb06fc6", 'default_graph_version' => "v3.2",
        ]);
        try {
            $response = $fb->get('/me?fields=id,name,email', $token);
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            return false;
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            return false;
        }
        return $user = $response->getGraphUser();
    }
}
