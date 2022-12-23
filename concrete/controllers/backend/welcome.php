<?php
namespace Concrete\Controller\Backend;

use Concrete\Core\Application\UserInterface\Welcome\WelcomeService;
use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Permission\Checker;
use Symfony\Component\HttpFoundation\JsonResponse;
use Concrete\Core\Application\UserInterface\Welcome\Type\Manager;
use Concrete\Core\User\User as CoreUser;

class Welcome extends AbstractController
{

    public function markAsViewed(string $handle)
    {
        $token = $this->app->make('token');
        $u = $this->app->make(CoreUser::class);
        $checker = new Checker();
        if ($token->validate() && $checker->canViewWelcomeContent()) {
            $driver = $this->app->make(Manager::class)->driver($handle);
            $driver->markModalAsViewed($u);
            return new JsonResponse(['viewed' => true]);
        }
        throw new UserMessageException(t('Access Denied.'));
    }


}
