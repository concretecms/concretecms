<?php
namespace Concrete\Core\Updater\ApplicationUpdate;

/**
 * @since 5.7.4
 */
class Version
{
    protected $version;
    protected $releaseNotes;
    protected $releaseNotesUrl;

    /**
     * @return mixed
     */
    public function getReleaseNotesURL()
    {
        return $this->releaseNotesURL;
    }

    /**
     * @param mixed $releaseNotesUrl
     */
    public function setReleaseNotesURL($releaseNotesURL)
    {
        $this->releaseNotesURL = $releaseNotesURL;
    }

    /**
     * @return mixed
     */
    public function getReleaseNotes()
    {
        return $this->releaseNotes;
    }

    /**
     * @param mixed $releaseNotes
     */
    public function setReleaseNotes($releaseNotes)
    {
        $this->releaseNotes = $releaseNotes;
    }

    /**
     * @return mixed
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param mixed $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }
}
