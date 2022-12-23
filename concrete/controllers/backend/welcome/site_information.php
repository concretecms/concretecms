<?php
namespace Concrete\Controller\Backend\Welcome;

use Concrete\Core\Application\UserInterface\Welcome\WelcomeService;
use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Permission\Checker;
use Symfony\Component\HttpFoundation\JsonResponse;
use Concrete\Core\Application\UserInterface\Welcome\Type\Manager;
use Concrete\Core\User\User as CoreUser;

class SiteInformation extends AbstractController
{

    public function submit()
    {
        $token = $this->app->make('token');
        $u = $this->app->make(CoreUser::class);
        if ($token->validate() && $u->isSuperUser()) {
            $driver = $this->app->make(Manager::class)->driver('site_information');
            $survey = $driver->getSurvey();
            $survey->getSaver()->saveFromRequest($this->request);
            $driver->markModalAsViewed($u);
            return new JsonResponse(['viewed' => true]);
        }
        throw new UserMessageException(t('Access Denied.'));
    }


}
