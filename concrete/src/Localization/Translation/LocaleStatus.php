<?php
namespace Concrete\Core\Localization\Translation;

use Concrete\Core\Localization\Translation\Local\Stats as LocalStats;
use Concrete\Core\Localization\Translation\Remote\Stats as RemoteStats;

class LocaleStatus
{
    /**
     * @var LocalRemoteCouple[]
     */
    protected $installedUpdated;

    /**
     * @var LocalRemoteCouple[]
     */
    protected $installedOutdated;

    /**
     * @var RemoteStats[]
     */
    protected $onlyRemote;

    /**
     * @var LocalStats[]
     */
    protected $onlyLocal;

    public function __construct()
    {
        $this->installedUpdated = [];
        $this->installedOutdated = [];
        $this->onlyRemote = [];
        $this->onlyLocal = [];
    }

    /**
     * @param LocalRemoteCouple $value
     *
     * @return $this
     */
    public function addInstalledUpdated($localeID, LocalRemoteCouple $value)
    {
        $this->installedUpdated[$localeID] = $value;

        return $this;
    }

    /**
     * @return LocalRemoteCouple[]
     */
    public function getInstalledUpdated()
    {
        return $this->installedUpdated;
    }

    /**
     * @param LocalRemoteCouple $value
     *
     * @return $this
     */
    public function addInstalledOutdated($localeID, LocalRemoteCouple $value)
    {
        $this->installedOutdated[$localeID] = $value;

        return $this;
    }

    /**
     * @return LocalRemoteCouple[]
     */
    public function getInstalledOutdated()
    {
        return $this->installedOutdated;
    }

    /**
     * @param RemoteStats $value
     *
     * @return $this
     */
    public function addOnlyRemote($localeID, RemoteStats $value)
    {
        $this->onlyRemote[$localeID] = $value;

        return $this;
    }

    /**
     * @return RemoteStats[]
     */
    public function getOnlyRemote()
    {
        return $this->onlyRemote;
    }

    /**
     * @param LocalStats $value
     *
     * @return $this
     */
    public function addOnlyLocal($localeID, LocalStats $value)
    {
        $this->onlyLocal[$localeID] = $value;

        return $this;
    }

    /**
     * @return RemoteStats[]
     */
    public function getOnlyLocal()
    {
        return $this->onlyLocal;
    }
}
