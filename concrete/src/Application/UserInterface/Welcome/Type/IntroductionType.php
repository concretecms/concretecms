<?php

namespace Concrete\Core\Application\UserInterface\Welcome\Type;

use Concrete\Core\Application\UserInterface\Welcome\Modal\IntroductionModal;
use Concrete\Core\Application\UserInterface\Welcome\Modal\ModalInterface;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\User\User;
use Concrete\Core\User\User as ConcreteUser;

class IntroductionType extends Type
{

    /**
     * @var Repository
     */
    protected $config;

    /**
     * @param Repository $config
     */
    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    public function getModal(): ModalInterface
    {
        return new IntroductionModal();
    }

    /**
     * Determines whether the introduction modal should be displayed. Ported almost directly from the original
     * `showHelpOverlay` method.
     *
     * @param User $user
     * @param array $modalDrivers
     * @return bool
     */
    public function showModal(User $user, array $modalDrivers): bool
    {
        $result = false;
        if ($this->config->get('concrete.misc.help_overlay')) {
            $timestamp = $user->config('MAIN_HELP_LAST_VIEWED');
            if (!$timestamp) {
                $result = true;
            }
        }
        return $result;
    }

    /**
     * Marks the modal as having been seen. Ported almost directly from the original `trackHelpOverlayDisplayed`
     * method
     *
     * @param ConcreteUser $user
     * @param ModalInterface $modal
     */
    public function trackModalDisplayed(User $user, ModalInterface $modal)
    {
        $user->saveConfig('MAIN_HELP_LAST_VIEWED', time());
    }

}
