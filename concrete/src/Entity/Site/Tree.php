<?php
namespace Concrete\Core\Entity\Site;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="SiteTrees")
 */
class Tree
{

    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $siteTreeID;

    /**
     * @ORM\Column(type="integer", options={"unsigned":true})
     */
    protected $siteHomePageID = 0;

    /**
     * @return mixed
     */
    public function getSiteHomePageID()
    {
        return $this->siteHomePageID;
    }

    /**
     * @param mixed $siteHomePageID
     */
    public function setSiteHomePageID($siteHomePageID)
    {
        $this->siteHomePageID = $siteHomePageID;
    }

    /**
     * @return mixed
     */
    public function getSiteTreeID()
    {
        return $this->siteTreeID;
    }





}
