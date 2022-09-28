<?php

namespace Concrete\Core\User\Group\Command;

use Concrete\Core\Foundation\Command\Command;
use Concrete\Core\User\Group\Command\Traits\GroupDetailsTrait;

class AddGroupCommand extends Command
{
    use GroupDetailsTrait;

    /**
     * @var int|null
     */
    protected $pkgID;

    /**
     * @var int|null
     */
    protected $forcedNewGroupID;

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

    public function getForcedNewGroupID(): ?int
    {
        return $this->forcedNewGroupID;
    }

    /**
     * @return $this
     */
    public function setForcedNewGroupID(?int $value): self
    {
        $this->forcedNewGroupID = $value;

        return $this;
    }
}
