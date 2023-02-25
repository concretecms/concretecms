<?php

namespace Concrete\Core\Application\UserInterface\Welcome;

use Concrete\Core\Application\UserInterface\Welcome\Modal\Modal;
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

    public function getModal(): ?ModalInterface
    {
        if ($this->checker->canViewWelcomeContent()) {
            $modal = new Modal();
            $drivers = $this->manager->getDrivers();
            foreach ($drivers as $driver) {
                /**
                 * @var $driver TypeInterface
                 */
                if ($driver->showModal($this->user, $drivers)) {
                    $slides = $driver->getSlides();
                    if (count($slides)) {
                        $modal->addSlides($slides);
                    }
                }
            }
            if (count($modal->getSlides()) > 0) {
                return $modal;
            }
        }
        return null;
    }
}
