<?php
namespace Concrete\Core\Updater;

class RemoteApplicationUpdate
{
    protected $identifier;
    protected $version;
    protected $date;
    protected $notes;
    protected $directDownloadURL;

    /**
     * @return mixed
     */
    public function getDirectDownloadURL()
    {
        return $this->directDownloadURL;
    }

    /**
     * @param mixed $directDownloadURL
     */
    public function setDirectDownloadURL($directDownloadURL)
    {
        $this->directDownloadURL = $directDownloadURL;
    }

    /**
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @param mixed $identifier
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
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

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param mixed $notes
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
    }
}
