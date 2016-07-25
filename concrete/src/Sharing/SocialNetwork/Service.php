<?php
namespace Concrete\Core\Sharing\SocialNetwork;

class Service
{
    protected $ssHandle;
    protected $ssName;
    protected $ssIcon;
    protected $customHTML;

    public function __construct($ssHandle, $ssName, $ssIcon, $customHTML = null)
    {
        $this->ssHandle = $ssHandle;
        $this->ssName = $ssName;
        $this->ssIcon = $ssIcon;
        $this->customHTML = $customHTML;
    }

    public function getHandle()
    {
        return $this->ssHandle;
    }

    public function getName()
    {
        return $this->ssName;
    }

    public function getDisplayName()
    {
        return h($this->getName());
    }

    public function getIcon()
    {
        return $this->ssIcon;
    }

    public function getServiceIconHTML()
    {
        if ($this->customHTML) {
            return $this->customHTML;
        } else {
            return '<i class="fa fa-' . $this->getIcon() . '"></i>';
        }
    }

    public static function getByHandle($ssHandle)
    {
        $services = ServiceList::get();
        foreach ($services as $s) {
            if ($s->getHandle() == $ssHandle) {
                return $s;
            }
        }
    }
}
