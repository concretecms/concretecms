<?php

namespace Concrete\Core\Application\UserInterface\Welcome;

use Concrete\Core\Application\UserInterface\Welcome\Modal\IntroductionModal;
use Concrete\Core\Application\UserInterface\Welcome\Modal\ModalInterface;
use Concrete\Core\Application\UserInterface\Welcome\Type\Manager;
use Concrete\Core\Application\UserInterface\Welcome\Type\TypeInterface;
use Concrete\Core\Permission\Checker;
use Concrete\Core\User\User;

class WelcomeService
{

    /**
     * @var Checker
     */
    protected $checker;

    /**
     * @var Manager
     */
    protected $manager;

    /**
     * @var User
     */
    protected $user;

    /**
     * @param Checker $checker
     */
    public function __construct(Manager $manager, Checker $checker, User $user)
    {
        $this->manager = $manager;
        $this->checker = $checker;
        $this->user = $user;
    }

    public function getModal(bool $trackView = true): ?ModalInterface
    {
        if ($this->checker->canViewWelcomeContent()) {
            $drivers = $this->manager->getDrivers();
            foreach ($drivers as $driver) {
                /**
                 * @var $driver TypeInterface
                 */
                if ($driver->showModal($this->user, $drivers)) {
                    $modal = $driver->getModal();
                    if ($modal) {
                        $driver->trackModalDisplayed($this->user, $modal);
                        return $modal;
                    }
                }
            }
        }
        return null;
    }
}
