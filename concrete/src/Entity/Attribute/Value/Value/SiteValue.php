<?php
namespace Concrete\Core\Entity\Attribute\Value\Value;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="atSite")
 */
class SiteValue extends AbstractValue
{
    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\Site\Site")
     * @ORM\JoinColumn(name="siteID", referencedColumnName="siteID")
     */
    protected $site;

    /**
     * @return mixed
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @param mixed $site
     */
    public function setSite($site)
    {
        $this->site = $site;
    }

    public function getValue()
    {
        return $this->site;
    }

    public function __toString()
    {
        if (is_object($this->site)) {
            return (string) $this->site->getSiteName();
        }
        return '';
    }
}
