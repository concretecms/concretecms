<?php

namespace Concrete\Core\User\Group\Command;

use Concrete\Core\Foundation\Command\CommandInterface;
use Concrete\Core\User\Group\Command\Traits\GroupDetailsTrait;

class AddGroupCommand implements CommandInterface
{

    use GroupDetailsTrait;

    /**
     * @var int
     */
    protected $pkgID = 0;

    public function setPackageID($pkgID)
    {
        $this->pkgID =$pkgID;
    }

    public function getPackageID() : int
    {
        return $this->pkgID;
    }

}