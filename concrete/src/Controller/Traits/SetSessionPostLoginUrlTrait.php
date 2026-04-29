<?php

namespace Concrete\Core\Controller\Traits;

use Concrete\Core\Site\InstallationService;
use Concrete\Core\User\PostLoginLocation;
use Concrete\Core\User\PostLoginLocationUrl;
trait SetSessionPostLoginUrlTrait
{

    public function redirect(): void
    {
        $pll = $this->app->make(PostLoginLocation::class);
        $urlHelper = $this->app->make(PostLoginLocationUrl::class);
        $rcURL = $urlHelper->getAllowedRedirectUrl($this->request->query->get('rcURL'));
        if ($rcURL !== '') {
            $pll->setSessionPostLoginUrl($rcURL);
        }
    }

}
