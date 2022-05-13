<?php
declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Client\Factory;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FacebookAuthenticationService
{
    public const KEY_ACCESS_TOKEN = 'access_token';

    public const DEFAULT_STATE = 'can_continue';

    public const QUERY_KEY_CODE = 'code';
    public const QUERY_KEY_STATE = 'state';
    public const QUERY_KEY_ERROR_CODE = 'error_code';
    public const QUERY_KEY_ERROR_REASON = 'error_reason';

    private FacebookApiUrlProvider $apiUrlProvider;
    private Factory $httpClientFactory;

    public function __construct(FacebookApiUrlProvider $apiUrlProvider, Factory $httpClientFactory)
    {
        $this->apiUrlProvider = $apiUrlProvider;
        $this->httpClientFactory = $httpClientFactory;
    }

    public function login(Request $request): void
    {
        if ($this->canLogin($request)) {
            $code = $request->query(self::QUERY_KEY_CODE);
            $oauthUrl = $this->apiUrlProvider->getOauthUrl($request->fullUrl(), $code);

            $oauthResult = $this->httpClientFactory->get($oauthUrl);

            if (Response::HTTP_OK === $oauthResult->status()) {
                $request->session()->put(self::KEY_ACCESS_TOKEN, $oauthResult->json(self::KEY_ACCESS_TOKEN));
            }
        }
    }

    public function logout(Request $request): void
    {
        $request->session()->remove(self::KEY_ACCESS_TOKEN);
    }

    public function getUserInfo(string $accessToken): array
    {
        $queryResult = $this->httpClientFactory->get(
            $this->apiUrlProvider->getMeUrl($accessToken)
        );

        $userInfo = [];
        if (Response::HTTP_OK === $queryResult->status()) {
            $userInfo = $queryResult->json();
        }

        return $userInfo;
    }

    private function canLogin(Request $request): bool
    {
        $state = $request->query(self::QUERY_KEY_STATE);
        $errorCode = $request->query(self::QUERY_KEY_ERROR_CODE);
        $errorReason = $request->query(self::QUERY_KEY_ERROR_REASON);

        return self::DEFAULT_STATE === $state && null === $errorCode && null === $errorReason;
    }
}
