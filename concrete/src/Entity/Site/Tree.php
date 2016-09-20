<?php
namespace Concrete\Core\Entity\Site;

use Concrete\Core\Site\Tree\TreeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="treeType", type="string")
 * @ORM\Table(
 *     name="SiteTrees"
 * )
 */
abstract class Tree implements TreeInterface
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

    public function getSiteHomePageObject()
    {
        return \Page::getByID($this->siteHomePageID);
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
