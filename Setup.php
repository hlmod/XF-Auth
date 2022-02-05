<?php
declare(strict_types=1);

/**
 * This file is a part of [HLModerators] Connected Account Add-On for XenForo v2.2.x.
 * All rights reserved.
 *
 * Developed by HLModerators.
 */

namespace HLModerators\Auth;


use XF\AddOn\AbstractSetup;
use XF\AddOn\StepRunnerInstallTrait;
use XF\AddOn\StepRunnerUninstallTrait;
use XF\AddOn\StepRunnerUpgradeTrait;
use XF\Entity\ConnectedAccountProvider;

class Setup extends AbstractSetup
{
    use StepRunnerInstallTrait;
    use StepRunnerUpgradeTrait;
    use StepRunnerUninstallTrait;

    public function installStep1()
    {
        /** @var ConnectedAccountProvider $provider */
        $provider = $this->app->em()->create('XF:ConnectedAccountProvider');
        $provider->bulkSet($this->getProviderDetails());
        $provider->save();
    }

    public function uninstallStep1()
    {
        /** @var ConnectedAccountProvider $provider */
        $provider = $this->app->em()->find('XF:ConnectedAccountProvider', $this->getProviderField('provider_id'));
        if (!$provider)
        {
            // Damn. Something already removed our record.
            return;
        }

        $provider->delete();
    }

    /**
     * @return array
     */
    protected function getProviderDetails(): array
    {
        return [
            'provider_id'       => 'hlmod',
            'provider_class'    => 'HLModerators\Auth:Provider\HLMod',
            'display_order'     => 200,
            'options'           => [],
        ];
    }

    /**
     * @param string $field
     */
    protected function getProviderField($field)
    {
        return $this->getProviderDetails()[$field] ?? null;
    }
}
