<?php
namespace Concrete\Core\Localization\Translation;

use Concrete\Core\Localization\Translation\Local\Stats as LocalStats;
use Concrete\Core\Localization\Translation\Remote\Stats as RemoteStats;

class LocalRemoteCouple
{
    /**
     * @var LocalStats
     */
    protected $localStats;

    /**
     * @var RemoteStats
     */
    protected $remoteStats;

    /**
     * @param LocalStats $localStats
     * @param RemoteStats $remoteStats
     */
    public function __construct(LocalStats $localStats, RemoteStats $remoteStats)
    {
        $this->localStats = $localStats;
        $this->remoteStats = $remoteStats;
    }

    /**
     * @return \Concrete\Core\Localization\Translation\Local\Stats
     */
    public function getLocalStats()
    {
        return $this->localStats;
    }

    /**
     * @return \Concrete\Core\Localization\Translation\Remote\Stats
     */
    public function getRemoteStats()
    {
        return $this->remoteStats;
    }
}
