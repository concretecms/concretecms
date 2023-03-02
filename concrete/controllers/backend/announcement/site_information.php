<?php
namespace Concrete\Controller\Backend\Announcement;

use Concrete\Core\Announcement\AnnouncementService;
use Concrete\Core\Announcement\Manager;
use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Error\UserMessageException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Concrete\Core\User\User as CoreUser;

class SiteInformation extends AbstractController
{

    public function submit()
    {
        $token = $this->app->make('token');
        $u = $this->app->make(CoreUser::class);
        if ($token->validate() && $u->isSuperUser()) {
            $driver = $this->app->make(Manager::class)->driver('collect_site_information');
            $survey = $driver->getSurvey();
            $survey->getSaver()->saveFromRequest($this->request);
            $announcementService = $this->app->make(AnnouncementService::class);
            $announcementService->markAnnouncementAsViewed('collect_site_information', $u);
            return new JsonResponse(['viewed' => true]);
        }
        throw new UserMessageException(t('Access Denied.'));
    }


}
