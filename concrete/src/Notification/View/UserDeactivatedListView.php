<?php

namespace Concrete\Core\Notification\View;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Entity\User\User;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @property \Concrete\Core\Entity\Notification\UserDeactivatedNotification $notification
 */
class UserDeactivatedListView extends StandardListView implements ApplicationAwareInterface
{

    use ApplicationAwareTrait;

    /**
     * The url resolver we use to build urls
     *
     * @var ResolverManagerInterface
     */
    protected $resolver;

    public function getIconClass()
    {
        return 'fas fa-user-times';
    }

    public function getTitle()
    {
        return t('User Deactivated');
    }

    /**
     * Build the description for this notification
     *
     * @return string
     */
    public function getActionDescription()
    {
        $entityManager = $this->getApplication()->make(EntityManagerInterface::class);

        /** @var User $user */
        $user = $entityManager->find(User::class, $this->notification->getUserID());
        $actor = null;

        $actorID = $this->notification->getActorID();
        if ($actorID) {
            /** @var User $actor */
            $actor = $entityManager->find(User::class, $actorID);
        }

        if ($actorID) {
            return t(
                '%s has been manually deactivated by %s',
                $this->getUserLink($user),
                $this->getUserLink($actor));
        }

        return t('%s has been automatically deactivated.', $this->getUserLink($user));
    }

    /**
     * Resolve a `<a...>...</a>` tag from a user entity (or null)
     * If null is passed, we return a placeholder that makes it clear the user has been deleted
     *
     * @param \Concrete\Core\Entity\User\User|null $user
     *
     * @return string
     */
    protected function getUserLink(User $user = null)
    {
        if (!$user) {
            return '<strong>' . t('Deleted User') . '</strong>';
        }

        $link = $this->getUrlResolver()->resolve(['/dashboard/users/search', $user->getUserID()]);
        $name = h($user->getUserName());
        return "<a href='$link'><strong>$name</strong></a>";
    }

    /**
     * Resolve an application instance
     *
     * Since this class is instantiated within a spot that has no awareness of the application, we have to resolve the
     * container manually using the facades. Ideally views would be decoupled from the value objects that they render.
     *
     * @return \Concrete\Core\Application\Application
     */
    protected function getApplication()
    {
        // @codeCoverageIgnoreStart
        if (!$this->app) {
            $this->app = Application::getFacadeApplication();
        }
        // @codeCoverageIgnoreEnd

        return $this->app;
    }

    /**
     * Resolve the url resolver instance
     *
     * @return \Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface
     */
    protected function getUrlResolver()
    {
        if (!$this->resolver) {
            $this->resolver = $this->getApplication()->make(ResolverManagerInterface::class);
        }

        return $this->resolver;
    }
}
