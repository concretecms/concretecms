<?php
namespace Concrete\Core\Entity\Site;

use Concrete\Core\Entity\LocaleTrait;
use Concrete\Core\Localization\Locale\LocaleInterface;
use Concrete\Core\Multilingual\Service\UserInterface\Flag;
use Concrete\Core\Site\Tree\TreeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="SiteLocales")
 */
class Locale implements LocaleInterface, TreeInterface
{

    use LocaleTrait;

    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $siteLocaleID;

    /**
     * @ORM\ManyToOne(targetEntity="Site", inversedBy="locales")
     * @ORM\JoinColumn(name="siteID", referencedColumnName="siteID")
     **/
    protected $site;

    /**
     * @ORM\OneToOne(targetEntity="SiteTree", cascade={"all"}, mappedBy="locale")
     * @ORM\JoinColumn(name="siteTreeID", referencedColumnName="siteTreeID")
     **/
    protected $tree;

    /**
     * @ORM\Column(type="boolean")
     */
    public $msIsDefault = false;

    /**
     * @return mixed
     */
    public function getLocaleID()
    {
        return $this->siteLocaleID;
    }

    /**
     * @return mixed
     */
    public function getIsDefault()
    {
        return $this->msIsDefault;
    }

    /**
     * @param mixed $msIsDefault
     */
    public function setIsDefault($msIsDefault)
    {
        $this->msIsDefault = $msIsDefault;
    }

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

    /**
     * @return mixed
     */
    public function getSiteTree()
    {
        return $this->tree;
    }

    /**
     * @param mixed $tree
     */
    public function setSiteTree($tree)
    {
        $this->tree = $tree;
    }

    public function getSiteTreeID()
    {
        return $this->getSiteTree()->getSiteTreeID();
    }

    public function getSiteTreeObject()
    {
        return $this->getSiteTree();
    }
}
