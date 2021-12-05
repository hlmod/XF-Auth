<?php
declare(strict_types=1);

/**
 * This file is a part of [HLModerators] Connected Account Add-On for XenForo v2.2.x.
 * All rights reserved.
 *
 * Developed by HLModerators.
 */

namespace HLModerators\Auth\ConnectedAccount\Service;


use OAuth\Common\Consumer\CredentialsInterface;
use OAuth\Common\Http\Client\ClientInterface;
use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\Common\Http\Uri\Uri;
use OAuth\Common\Http\Uri\UriInterface;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\Common\Token\TokenInterface;
use OAuth\OAuth2\Service\AbstractService;
use OAuth\OAuth2\Token\StdOAuth2Token;

class HLMod extends AbstractService
{
    const SCOPE_USER_READ = 'user:read';

    public function __construct(CredentialsInterface $credentials, ClientInterface $httpClient,
                                TokenStorageInterface $storage, $scopes = array(), UriInterface $baseApiUri = null,
                                $stateParameterInAutUrl = false, $apiVersion = "")
    {
        parent::__construct($credentials, $httpClient, $storage, $scopes,
            $baseApiUri, $stateParameterInAutUrl, $apiVersion);

        $this->baseApiUri = new Uri('https://hlmod.ru/api/');
    }

    protected function parseAccessTokenResponse($responseBody): TokenInterface
    {
        $data = json_decode($responseBody, true);
        if (!is_array($data) || !($data['success'] ?? false))
        {
            throw new TokenResponseException('Unable to parse response.');
        }
        elseif (isset($data['error']))
        {
            throw new TokenResponseException('Error in retrieving token: "' . $data['error'] . '"');
        }

        $token = new StdOAuth2Token();
        $token->setAccessToken($data['access_token']);
        $token->setRefreshToken($data['refresh_token']);
        $token->setEndOfLife($data['server_time'] + $data['expires_in']);

        return $token;
    }

    public function getAuthorizationEndpoint(): UriInterface
    {
        return new Uri('https://hlmod.ru/login/oauth/');
    }

    protected function getAuthorizationMethod(): int
    {
        return -1;
    }

    public function getAccessTokenEndpoint(): UriInterface
    {
        return new Uri('https://hlmod.ru/api/auth/hlm-oauth/token/');
    }

    protected function getExtraApiHeaders(): array
    {
        $token = $this->storage->retrieveAccessToken($this->service());
        // We're already know: this token isn't expired (checked in `request()` method).

        return [
            'XF-Api-Key' => $token->getAccessToken()
        ];
    }
}
