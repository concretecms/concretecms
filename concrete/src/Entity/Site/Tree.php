<?php
namespace Concrete\Core\Entity\Site;

use Concrete\Core\Site\Tree\TreeInterface;
use Concrete\Core\Site\TypeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="treeType", type="string")
 * @ORM\Table(
 *     name="SiteTrees"
 * )
 */
abstract class Tree implements TreeInterface, TypeInterface
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
        $home = \Page::getByID($this->siteHomePageID);
        if (is_object($home) && !$home->isError()) {
            return $home;
        }
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

    public function getSiteTreeObject()
    {
        return $this;
    }

    abstract public function getDisplayName();
}
