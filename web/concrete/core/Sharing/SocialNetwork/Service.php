<?php
namespace Concrete\Core\Sharing\SocialNetwork;

class Service
{

    protected $ssID;
    protected $ssName;
    protected $ssIcon;

    public function __construct($ssID, $ssName, $ssIcon)
    {
        $this->ssID = $ssID;
        $this->ssName = $ssName;
        $this->ssIcon = $ssIcon;
    }

    public function getID()
    {
        return $this->ssID;
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

    public static function getByID($ssID)
    {
        $services = ServiceList::get();
        foreach($services as $s) {
            if ($s->getID() == $ssID) {
                return $s;
            }
        }
    }

}