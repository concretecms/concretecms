<?php
namespace Concrete\Core\Permission\Access;

use Concrete\Core\Entity\Board\Board;

class BoardAccess extends Access implements SiteAccessInterface
{

    public function getSite()
    {
        /**
         * @var $board Board
         */
        $board = $this->getPermissionObject();
        return $board->getSite();
    }

}
