<?php

namespace Concrete\Core\Page\Theme\Command;

use Concrete\Core\Entity\Page\Theme\CustomSkin;
use Concrete\Core\Foundation\Command\CommandInterface;

class DeleteCustomSkinCommand implements CommandInterface
{

    /**
     * @var CustomSkin
     */
    protected $customSkin;

    /**
     * @return CustomSkin
     */
    public function getCustomSkin(): CustomSkin
    {
        return $this->customSkin;
    }

    /**
     * @param CustomSkin $customSkin
     */
    public function setCustomSkin(CustomSkin $customSkin): void
    {
        $this->customSkin = $customSkin;
    }


}
