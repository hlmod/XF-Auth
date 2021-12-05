<?php
declare(strict_types=1);

/**
 * This file is a part of [HLModerators] Connected Account Add-On for XenForo v2.2.x.
 * All rights reserved.
 *
 * Developed by HLModerators.
 */

namespace HLModerators\Auth\ConnectedAccount\Provider;


use XF\ConnectedAccount\Provider\AbstractProvider;
use XF\Entity\ConnectedAccountProvider;

class HLMod extends AbstractProvider
{
    public function getOAuthServiceName(): string
    {
        return 'HLModerators\Auth:Service\HLMod';
    }

    public function getProviderDataClass(): string
    {
        return 'HLModerators\Auth:ProviderData\HLMod';
    }

    public function getDefaultOptions(): array
    {
        return [
            'client_id' => '',
            'client_secret' => ''
        ];
    }

    public function getOAuthConfig(ConnectedAccountProvider $provider, $redirectUri = null): array
    {
        return [
            'key' => $provider->options['client_id'],
            'secret' => $provider->options['client_secret'],
            'scopes' => ['user:read'],
            'redirect' => $redirectUri ?: $this->getRedirectUri($provider)
        ];
    }
}
