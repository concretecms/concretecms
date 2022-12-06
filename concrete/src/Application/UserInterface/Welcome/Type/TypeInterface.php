<?php

namespace Concrete\Core\Application\UserInterface\Welcome\Type;

use Concrete\Core\Application\UserInterface\Welcome\Modal\ModalInterface;
use Concrete\Core\User\User;

interface TypeInterface
{

    public function getModal(): ModalInterface;

    public function showModal(User $user, array $modalDrivers): bool;

    public function trackModalDisplayed(User $user, ModalInterface $modal);

}
