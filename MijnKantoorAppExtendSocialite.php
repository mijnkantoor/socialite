<?php


namespace MijnKantoor\Socialite;

use SocialiteProviders\Manager\SocialiteWasCalled;

class MijnKantoorAppExtendSocialite
{
    /**
     * @param SocialiteWasCalled $socialiteWasCalled
     */
    public function handle(SocialiteWasCalled $socialiteWasCalled)
    {
        $socialiteWasCalled->extendSocialite('mijnkantoorapp', Provider::class);
    }
}
