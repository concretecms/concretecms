<?php
namespace Concrete\Core\Navigation\Modifier;

use Concrete\Core\Navigation\NavigationInterface;

interface ModifierInterface
{

    public function modify(NavigationInterface $query);

}
