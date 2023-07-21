<?php

namespace Concrete\Core\Announcement\Controller;

use Concrete\Core\Announcement\Controller\Traits\SingleSlideTrait;
use Concrete\Core\Announcement\Slide\Slide;
use Concrete\Core\Announcement\Slide\SlideInterface;
use Concrete\Core\SiteInformation\SiteInformationSurvey;
use Concrete\Core\SiteInformation\SurveyInterface;
use Concrete\Core\User\User;

class CollectSiteInformationController extends AbstractController
{

    use SingleSlideTrait;

    public function shouldDisplayAnnouncementToUser(User $user): bool
    {
        return $user->isSuperUser();
    }

    public function onViewAnnouncement(User $user)
    {
        $config = $this->app->make('config/database');
        $config->save('app.site_information.viewed', true);
    }

    public function getSurvey(): SurveyInterface
    {
        return $this->app->make(SiteInformationSurvey::class);
    }

    public function getSlide(User $user): SlideInterface
    {
        return new Slide('concrete-announcement-collect-site-information-slide', ['survey' => $this->getSurvey()->render()]);
    }


}
