<?php
declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\FacebookApiUrlProvider;
use Tests\AbstractTestCase;

/**
 * @group unit
 */
class FacebookApiUrlProviderTest extends AbstractTestCase
{
    private const FACEBOOK_API_LOGIN_URL = 'https://login.test.com';
    private const FACEBOOK_API_OAUTH_URL = 'https://api.test.com';
    private const CLIENT_ID = '12345';
    private const CLIENT_SECRET = '1a2b3c';

    private const REDIRECT_URI = '';
    private const SOME_STATE = 'some_state';
    private const CODE = '98765';
    private const ACCESS_TOKEN = 'token123';

    private FacebookApiUrlProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->provider = new FacebookApiUrlProvider(
            self::FACEBOOK_API_LOGIN_URL,
            self::FACEBOOK_API_OAUTH_URL,
            self::CLIENT_ID, self::CLIENT_SECRET
        );
    }

    /**
     * @test
     *
     * @return void
     */
    public function getLoginUrl(): void
    {
        $loginUrl = self::FACEBOOK_API_LOGIN_URL;
        $clientId = self::CLIENT_ID;
        $redirectUri = self::REDIRECT_URI;
        $state = self::SOME_STATE;

        $expected = "{$loginUrl}?client_id={$clientId}&redirect_uri={$redirectUri}&state={$state}";

        self::assertSame($expected, $this->provider->getLoginUrl(self::REDIRECT_URI, self::SOME_STATE));
    }

    /**
     * @test
     *
     * @return void
     */
    public function getOauthUrl(): void
    {
        $oauthUrl = self::FACEBOOK_API_OAUTH_URL;
        $clientId = self::CLIENT_ID;
        $clientSecret = self::CLIENT_SECRET;
        $redirectUri = self::REDIRECT_URI;
        $code = self::CODE;

        $expected = "{$oauthUrl}/oauth/access_token?"
            . "client_id={$clientId}&redirect_uri={$redirectUri}&client_secret={$clientSecret}&code={$code}";

        self::assertSame($expected, $this->provider->getOauthUrl(self::REDIRECT_URI, self::CODE));
    }

    /**
     * @test
     *
     * @return void
     */
    public function getMeUrl(): void
    {
        $oauthUrl = self::FACEBOOK_API_OAUTH_URL;
        $accessToken = self::ACCESS_TOKEN;

        $expected = "{$oauthUrl}/me?access_token={$accessToken}";

        self::assertSame($expected, $this->provider->getMeUrl(self::ACCESS_TOKEN));
    }
}
