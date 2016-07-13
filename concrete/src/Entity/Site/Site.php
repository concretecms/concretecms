<?php
namespace Concrete\Core\Entity\Site;

use Concrete\Core\Application\Application;
use Concrete\Core\Site\Config\Liaison;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="Sites")
 */
class Site
{

    protected $siteConfig;

    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $siteID;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $siteIsDefault = false;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    protected $siteHandle;

    public function __construct($appConfigRepository)
    {
        $this->updateSiteConfigRepository($appConfigRepository);
    }

    public function updateSiteConfigRepository($appConfigRepository)
    {
        $this->siteConfig = new Liaison($appConfigRepository, $this);
    }

    public function getConfigRepository()
    {
        return $this->siteConfig;
    }

    /**
     * @return mixed
     */
    public function getSiteID()
    {
        return $this->siteID;
    }

    /**
     * @return mixed
     */
    public function getSiteHandle()
    {
        return $this->siteHandle;
    }

    /**
     * @param mixed $siteHandle
     */
    public function setSiteHandle($siteHandle)
    {
        $this->siteHandle = $siteHandle;
    }

    /**
     * @return mixed
     */
    public function isDefault()
    {
        return $this->siteIsDefault;
    }

    /**
     * @param mixed $siteIsDefault
     */
    public function setIsDefault($siteIsDefault)
    {
        $this->siteIsDefault = $siteIsDefault;
    }

    /**
     * @return mixed
     */
    public function getSiteName()
    {
        return $this->getConfigRepository()->get('name');
    }

    public function setSiteName($name)
    {
        return $this->getConfigRepository()->save('name', $name);
    }



}
