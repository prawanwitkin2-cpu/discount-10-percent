<?php
declare(strict_types=1);

class GoogleAuthService
{
    private \Google\Client $client;

    public function __construct()
    {
        $config = require BASE_PATH . '/config/oauth.php';
        
        $this->client = new \Google\Client();
        $this->client->setClientId($config['client_id']);
        $this->client->setClientSecret($config['client_secret']);
        $this->client->setRedirectUri($config['redirect_uri']);
        
        $this->client->addScope('email');
        $this->client->addScope('profile');
    }

    public function getAuthUrl(): string
    {
        return $this->client->createAuthUrl();
    }

    public function authenticate(string $code): ?array
    {
        $token = $this->client->fetchAccessTokenWithAuthCode($code);
        
        if (isset($token['error'])) {
            return null;
        }

        $this->client->setAccessToken($token['access_token']);
        
        $google_oauth = new \Google\Service\Oauth2($this->client);
        $google_account_info = $google_oauth->userinfo->get();
        
        return [
            'google_id' => $google_account_info->id,
            'email' => $google_account_info->email,
            'name' => $google_account_info->name,
            'picture' => $google_account_info->picture,
        ];
    }
}
