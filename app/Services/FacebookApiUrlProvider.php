<?php
declare(strict_types=1);

namespace App\Services;

class FacebookApiUrlProvider
{
    private string $loginUrl;
    private string $oauthUrl;
    private string $clientId;
    private string $clientSecret;

    public function __construct(
        string $facebookApiLoginUrl,
        string $facebookApiOauthUrl,
        string $clientId,
        string $clientSecret
    ) {
        $this->loginUrl = $facebookApiLoginUrl;
        $this->oauthUrl = $facebookApiOauthUrl;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    public function getLoginUrl(string $redirectUri, string $state): string
    {
        return sprintf(
            "%s?client_id=%s&redirect_uri=%s&state=%s",
            $this->loginUrl,
            $this->clientId,
            $redirectUri,
            $state
        );
    }

    public function getOauthUrl(string $redirectUri, string $code): string
    {
        return sprintf(
            "%s/oauth/access_token?client_id=%s&redirect_uri=%s&client_secret=%s&code=%s",
            $this->oauthUrl,
            $this->clientId,
            $redirectUri,
            $this->clientSecret,
            $code
        );
    }

    public function getMeUrl(string $accessToken): string
    {
        return sprintf(
            "%s/me?access_token=%s",
            $this->oauthUrl,
            $accessToken
        );
    }
}
