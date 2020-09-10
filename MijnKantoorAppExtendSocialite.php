<?php


namespace Mijnkantoor\Socialite;

use SocialiteProviders\Manager\SocialiteWasCalled;

class MijnKantoorAppExtendSocialite
{
    /**
     * Execute the provider.
     */
    public function handle(SocialiteWasCalled $socialiteWasCalled)
    {
        $socialiteWasCalled->extendSocialite('mijnkantoorapp', Provider::class);
    }
}
