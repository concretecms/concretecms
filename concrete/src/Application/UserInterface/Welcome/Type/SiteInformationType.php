<?php

namespace Concrete\Core\Application\UserInterface\Welcome\Type;

use Concrete\Core\Application\UserInterface\Welcome\Modal\Slide\Slide;
use Concrete\Core\Application\UserInterface\Welcome\Modal\Slide\SlideInterface;
use Concrete\Core\Application\UserInterface\Welcome\Type\Traits\SingleSlideTrait;
use Concrete\Core\SiteInformation\SiteInformationSurvey;
use Concrete\Core\SiteInformation\SurveyInterface;
use Concrete\Core\User\User;

class SiteInformationType extends Type
{

    use SingleSlideTrait;

    public function showModal(User $user, array $modalDrivers): bool
    {
        if ($user->isSuperUser()) {
            $config = $this->app->make('config/database');
            if (!$config->get('app.site_information.viewed')) {
                return true;
            }
        }
        return false;
    }

    public function markModalAsViewed(User $user)
    {
        $config = $this->app->make('config/database');
        $config->save('app.site_information.viewed', true);
    }

    public function getSurvey(): SurveyInterface
    {
        return $this->app->make(SiteInformationSurvey::class);
    }

    public function getSlide(): SlideInterface
    {
        return new Slide('concrete-welcome-content-site-information', ['survey' => $this->getSurvey()->render()]);
    }


}
