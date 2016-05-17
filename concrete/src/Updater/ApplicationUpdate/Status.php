<?php
namespace Concrete\Core\Updater\ApplicationUpdate;

class Status
{
    protected $status;
    protected $safety;

    /**
     * @return mixed
     */
    public function getSafety()
    {
        return $this->safety;
    }

    /**
     * @param mixed $safety
     */
    public function setSafety($safety)
    {
        $this->safety = $safety;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getJSONObject()
    {
        $o = new \stdClass();
        $o->status = $this->getStatus();
        $o->safety = $this->getSafety();

        return $o;
    }
}
