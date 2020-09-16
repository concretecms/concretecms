<?php
namespace Concrete\Core\Cache\Command;

use Concrete\Core\Foundation\Command\Command;

class ClearCacheCommand extends Command
{

    /**
     * @var bool
     */
    private $clearGlobalAreas = true;

    /**
     * @return bool
     */
    public function doClearGlobalAreas(): bool
    {
        return $this->clearGlobalAreas;
    }

    /**
     * @param bool $clearGlobalAreas
     */
    public function setClearGlobalAreas(bool $clearGlobalAreas): void
    {
        $this->clearGlobalAreas = $clearGlobalAreas;
    }

}
