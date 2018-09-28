<?php
namespace Concrete\Core\Entity\Attribute\Value\Value;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="atSelectedSocialLinks")
 */
class SelectedSocialLink
{
    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $avsID;

    /**
     * @ORM\ManyToOne(targetEntity="SocialLinksValue", inversedBy="links")
     * @ORM\JoinColumn(name="avID", referencedColumnName="avID")
     */
    protected $value;

    /**
     * @ORM\Column(type="string")
     */
    protected $service;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $serviceInfo;

    /**
     * @return mixed
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @param mixed $service
     */
    public function setService($service)
    {
        $this->service = $service;
    }

    /**
     * @return mixed
     */
    public function getServiceInfo()
    {
        return $this->serviceInfo;
    }

    /**
     * @param mixed $serviceInfo
     */
    public function setServiceInfo($serviceInfo)
    {
        $this->serviceInfo = $serviceInfo;
    }

    /**
     * @return mixed
     */
    public function getAttributeValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setAttributeValue($value)
    {
        $this->value = $value;
    }
}
