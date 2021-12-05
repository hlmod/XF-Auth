<?php
declare(strict_types=1);

/**
 * This file is a part of [HLModerators] Connected Account Add-On for XenForo v2.2.x.
 * All rights reserved.
 *
 * Developed by HLModerators.
 */

namespace HLModerators\Auth\ConnectedAccount\ProviderData;


use XF\ConnectedAccount\ProviderData\AbstractProviderData;

class HLMod extends AbstractProviderData
{
    public function getDefaultEndpoint()
    {
        return 'me';
    }

    public function getProviderKey(): int
    {
        return $this->requestFromEndpoint('user_id');
    }

    public function getUsername(): string
    {
        return $this->requestFromEndpoint('username');
    }

    public function getProfileLink(): string
    {
        return $this->requestFromEndpoint('view_url');
    }

    public function getAvatarUrl(): string
    {
        return $this->requestFromEndpoint('avatar_urls')['o'];
    }

    public function requestFromEndpoint($key = null, $method = 'GET', $endpoint = null)
    {
        $endpoint = $endpoint ?: $this->getDefaultEndpoint();
        $isDefaultEndpoint = ($endpoint == $this->getDefaultEndpoint());

        $cacheKey = $isDefaultEndpoint ? 'me' : $key;
        $response = parent::requestFromEndpoint($cacheKey, $method, $endpoint);

        return $isDefaultEndpoint && $key !== null ?
            $response[$key] : $response;
    }
}
