<?php
namespace Concrete\Core\Entity\Attribute\Value\Value;

/**
 * @Entity
 * @Table(name="SocialLinksAttributeSelectedLinks")
 */
class SelectedSocialLink
{
    /**
     * @Id @Column(type="integer", options={"unsigned":true})
     * @GeneratedValue(strategy="AUTO")
     */
    protected $avsID;

    /**
     * @ManyToOne(targetEntity="SocialLinksValue")
     * @JoinColumn(name="avID", referencedColumnName="avID")
     */
    protected $value;

    /**
     * @Column(type="string")
     */
    protected $service;

    /**
     * @Column(type="string", nullable=true)
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
