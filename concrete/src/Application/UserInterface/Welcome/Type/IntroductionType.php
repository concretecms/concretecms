<?php

namespace Concrete\Core\Application\UserInterface\Welcome\Type;

use Concrete\Core\Application\UserInterface\Welcome\Modal\ModalInterface;
use Concrete\Core\Application\UserInterface\Welcome\Modal\Slide\Slide;
use Concrete\Core\Application\UserInterface\Welcome\Type\Traits\SingleSlideTrait;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Filesystem\ElementManager;
use Concrete\Core\User\User;
use Concrete\Core\User\User as ConcreteUser;

class IntroductionType extends Type
{

    use SingleSlideTrait;

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
    public function markModalAsViewed(User $user)
    {
        $user->saveConfig('MAIN_HELP_LAST_VIEWED', time());
    }

    public function getSlide()
    {
        $body = $this->app->make(ElementManager::class)
            ->get('help/introduction')
            ->getContents();
        return new Slide('concrete-welcome-content-help', ['body' => $body]);
    }


}
