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
     * @var bool
     */
    protected $logCacheClear = false;

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

    public function logCacheClear(): bool
    {
        return $this->logCacheClear;
    }

    public function setLogCacheClear(bool $logCacheClear): void
    {
        $this->logCacheClear = $logCacheClear;
    }

    public function isClearGlobalAreas(): bool
    {
        return $this->clearGlobalAreas;
    }




}
