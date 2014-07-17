<?php
namespace Concrete\Core\Sharing\SocialNetwork;

class Service
{

    protected $ssHandle;
    protected $ssName;
    protected $ssIcon;

    public function __construct($ssHandle, $ssName, $ssIcon)
    {
        $this->ssHandle = $ssHandle;
        $this->ssName = $ssName;
        $this->ssIcon = $ssIcon;
    }

    public function getHandle()
    {
        return $this->ssHandle;
    }

    public function getName()
    {
        return $this->ssName;
    }

    public function getIcon()
    {
        return $this->ssIcon;
    }

    public function getServiceIconHTML()
    {
        return '<i class="fa fa-' . $this->getIcon() . '"></i>';
    }

    public static function getByHandle($ssHandle)
    {
        $services = ServiceList::get();
        foreach($services as $s) {
            if ($s->getHandle() == $ssHandle) {
                return $s;
            }
        }
    }

}