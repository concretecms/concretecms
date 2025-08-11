<?php
namespace Concrete\Controller\Backend;

use Concrete\Core\Announcement\AnnouncementService;
use Concrete\Core\Announcement\Manager;
use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Permission\Checker;
use Symfony\Component\HttpFoundation\JsonResponse;
use Concrete\Core\User\User as CoreUser;

class Announcement extends AbstractController
{

    public function markAsViewed(string $handle)
    {
        $token = $this->app->make('token');
        $u = $this->app->make(CoreUser::class);
        $checker = new Checker();
        if ($token->validate() && $checker->canViewAnnouncementContent()) {
            $announcementService = $this->app->make(AnnouncementService::class);
            $announcementService->markAnnouncementAsViewed($handle, $u);
            return new JsonResponse(['viewed' => true]);
        }
        throw new UserMessageException(t('Access Denied.'));
    }


}
