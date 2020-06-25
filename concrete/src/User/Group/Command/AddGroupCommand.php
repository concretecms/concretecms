<?php

namespace Concrete\Core\User\Group\Command;

use Concrete\Core\Foundation\Command\CommandInterface;
use Concrete\Core\User\Group\Command\Traits\GroupDetailsTrait;

class AddGroupCommand implements CommandInterface
{
    use GroupDetailsTrait;

    /**
     * @var int|null
     */
    protected $pkgID;

    public function getPackageID(): ?int
    {
        return $this->pkgID;
    }

    /**
     * @return $this
     */
    public function setPackageID(?int $pkgID): object
    {
        $this->pkgID = $pkgID;

        return $this;
    }
}
