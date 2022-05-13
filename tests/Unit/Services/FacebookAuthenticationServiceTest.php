<?php
declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\FacebookApiUrlProvider;
use App\Services\FacebookAuthenticationService;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Mockery;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Tests\AbstractTestCase;

/**
 * @group unit
 */
class FacebookAuthenticationServiceTest extends AbstractTestCase
{
    private const SOME_CODE = 'some_code';

    private const OAUTH_URL = 'oauth_url';
    private const FULL_URL = 'full_url';

    private const ACCESS_TOKEN = '...some token data';

    private Mockery\MockInterface|FacebookApiUrlProvider $apiUrlProviderMock;
    private Mockery\MockInterface|Factory $httpClientFactory;

    private FacebookAuthenticationService $service;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->apiUrlProviderMock = Mockery::mock(FacebookApiUrlProvider::class);
        $this->httpClientFactory = Mockery::mock(Factory::class);

        $this->service = new FacebookAuthenticationService($this->apiUrlProviderMock, $this->httpClientFactory);
    }

    /**
     * @test
     *
     * @return void
     */
    public function login(): void
    {
        $requestMock = $this->getPreparedRequestMock();

        $this->apiUrlProviderMock
            ->shouldReceive('getOauthUrl')
            ->once()
            ->with(self::FULL_URL, self::SOME_CODE)
            ->andReturn(self::OAUTH_URL);

        $oauthResultMock = Mockery::mock(Response::class);
        $oauthResultMock
            ->shouldReceive('status')
            ->once()
            ->withNoArgs()
            ->andReturn(SymfonyResponse::HTTP_OK);

        $oauthResultMock
            ->shouldReceive('json')
            ->once()
            ->with(FacebookAuthenticationService::KEY_ACCESS_TOKEN)
            ->andReturn(self::ACCESS_TOKEN);

        $this->httpClientFactory
            ->shouldReceive('get')
            ->once()
            ->with(self::OAUTH_URL)
            ->andReturn($oauthResultMock);

        $this->service->login($requestMock);
    }

    /**
     * @test
     *
     * @return void
     */
    public function logout(): void
    {
        $requestMock = Mockery::mock(Request::class);
        $sessionMock = Mockery::mock(Session::class)
            ->shouldReceive('remove')
            ->once()
            ->with(FacebookAuthenticationService::KEY_ACCESS_TOKEN)
            ->getMock();

        $this->mockRequestWithSession($requestMock, $sessionMock);

        $this->service->logout($requestMock);
    }

    /**
     * @test
     * @dataProvider getUserInfoDataProvider
     *
     * @param int $status
     * @param array $expectedUserData
     *
     * @return void
     */
    public function getUserInfo(int $status, array $expectedUserData): void
    {
        $this->apiUrlProviderMock
            ->shouldReceive('getMeUrl')
            ->once()
            ->with(self::ACCESS_TOKEN)
            ->andReturn(self::OAUTH_URL);

        $userResultMock = Mockery::mock(Response::class);
        $userResultMock
            ->shouldReceive('status')
            ->once()
            ->withNoArgs()
            ->andReturn($status);

        if (false === empty($expectedUserData)) {
            $userResultMock
                ->shouldReceive('json')
                ->once()
                ->withNoArgs()
                ->andReturn($expectedUserData);
        }

        $this->httpClientFactory
            ->shouldReceive('get')
            ->once()
            ->with(self::OAUTH_URL)
            ->andReturn($userResultMock);

        self::assertSame($expectedUserData, $this->service->getUserInfo(self::ACCESS_TOKEN));
    }

    public function getUserInfoDataProvider(): array
    {
        return [
            'with some data' => [
                'status' => SymfonyResponse::HTTP_OK,
                'expected' => ['...some data']
            ],
            'with empty data' => [
                'status' => SymfonyResponse::HTTP_BAD_REQUEST,
                'expected' => [],
            ],
        ];
    }

    private function getPreparedRequestMock(): Request|Mockery\MockInterface
    {
        $requestMock = Mockery::mock(Request::class);

        $requestMock
            ->shouldReceive('query')
            ->once()
            ->with(FacebookAuthenticationService::QUERY_KEY_STATE)
            ->andReturn(FacebookAuthenticationService::DEFAULT_STATE);

        $requestMock
            ->shouldReceive('query')
            ->once()
            ->with(FacebookAuthenticationService::QUERY_KEY_ERROR_CODE)
            ->andReturn(null);

        $requestMock
            ->shouldReceive('query')
            ->once()
            ->with(FacebookAuthenticationService::QUERY_KEY_ERROR_REASON)
            ->andReturn(null);

        $requestMock
            ->shouldReceive('query')
            ->once()
            ->with(FacebookAuthenticationService::QUERY_KEY_CODE)
            ->andReturn(self::SOME_CODE);

        $sessionMock = Mockery::mock(Session::class)
            ->shouldReceive('put')
            ->once()
            ->with(FacebookAuthenticationService::KEY_ACCESS_TOKEN, self::ACCESS_TOKEN)
            ->getMock();

        $this->mockRequestWithSession($requestMock, $sessionMock);

        $requestMock
            ->shouldReceive('fullUrl')
            ->once()
            ->withNoArgs()
            ->andReturn(self::FULL_URL);

        return $requestMock;
    }

    private function mockRequestWithSession(
        Mockery\MockInterface|Request $requestMock,
        Mockery\MockInterface|Request $sessionMock
    ): void {
        $requestMock
            ->shouldReceive('session')
            ->once()
            ->withNoArgs()
            ->andReturn($sessionMock);
    }
}
