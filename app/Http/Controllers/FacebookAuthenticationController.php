<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\FacebookApiUrlProvider;
use App\Services\FacebookAuthenticationService;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\View\View;
use JetBrains\PhpStorm\ArrayShape;

class FacebookAuthenticationController extends BaseController
{
    public const ACTION_INDEX = 'index';
    public const ACTION_LOGIN = 'login';
    public const ACTION_LOGOUT = 'logout';

    private const USER_DATA_KEY = 'user_data';
    private const LOGIN_URL_KEY = 'login_url';
    private const USER_INFO_KEY = 'user_info';
    private const LOGOUT_URL_KEY = 'logout_url';

    private const INDEX_VIEW_FACEBOOK = 'facebook';

    private FacebookAuthenticationService $authenticationService;
    private FacebookApiUrlProvider $apiUrlProvider;
    private UrlGenerator $urlGenerator;

    public function __construct(
        FacebookAuthenticationService $authenticationService,
        FacebookApiUrlProvider $apiUrlProvider,
        UrlGenerator $urlGenerator
    ) {
        $this->authenticationService = $authenticationService;
        $this->apiUrlProvider = $apiUrlProvider;
        $this->urlGenerator = $urlGenerator;
    }

    public function index(Request $request): View
    {
        $userData = $this->getUserData($request);
        $resultData = $this->getPreparedResultData($userData);

        return view(self::INDEX_VIEW_FACEBOOK, $resultData);
    }

    public function login(Request $request): RedirectResponse
    {
        $this->authenticationService->login($request);

        return redirect()->action([__CLASS__, self::ACTION_INDEX]);
    }

    public function logout(Request $request): RedirectResponse
    {
        $this->authenticationService->logout($request);

        return redirect()->action([__CLASS__, self::ACTION_INDEX]);
    }

    private function getUserData(Request $request): array
    {
        $accessToken = $request->session()->get(FacebookAuthenticationService::KEY_ACCESS_TOKEN);

        if (null === $accessToken) {
            $userData = [];
        } else {
            $userData = [
                self::USER_INFO_KEY => $this->authenticationService->getUserInfo($accessToken),
                self::LOGOUT_URL_KEY => $this->urlGenerator->action([__CLASS__, self::ACTION_LOGOUT]),
            ];
        }

        return $userData;
    }

    #[ArrayShape([self::LOGIN_URL_KEY => "string", self::USER_DATA_KEY => "array"])]
    private function getPreparedResultData(array $userData): array
    {
        return [
            self::LOGIN_URL_KEY => $this->apiUrlProvider->getLoginUrl(
                $this->urlGenerator->action([__CLASS__, self::ACTION_LOGIN]),
                FacebookAuthenticationService::DEFAULT_STATE
            ),
            self::USER_DATA_KEY => $userData,
        ];
    }
}
