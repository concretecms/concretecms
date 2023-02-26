<?php

namespace Concrete\Core\Application\UserInterface\Welcome\Type;

use Concrete\Core\Application\UserInterface\Welcome\Modal\ModalInterface;
use Concrete\Core\Application\UserInterface\Welcome\Modal\Slide\SlideInterface;
use Concrete\Core\User\User;

interface TypeInterface
{

    public function showModal(User $user, array $modalDrivers): bool;

    public function markModalAsViewed(User $user);

    /**
     * @return SlideInterface[]
     */
    public function getSlides(User $user): array;

}
