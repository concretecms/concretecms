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

    public function showAnnouncement(User $user, array $announcements): bool
    {
        if ($user->isSuperUser()) {
            $config = $this->app->make('config/database');
            if (!$config->get('app.site_information.viewed')) {
                return true;
            }
        }
        return false;
    }

    public function markAnnouncementAsViewed(User $user)
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
