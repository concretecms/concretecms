<?php

namespace Concrete\Core\Entity\Site;

use Concrete\Core\Entity\LocaleTrait;
use Concrete\Core\Localization\Locale\LocaleInterface;
use Concrete\Core\Site\Tree\TreeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a site locale.
 *
 * @ORM\Entity
 * @ORM\Table(name="SiteLocales")
 * @ORM\EntityListeners({"\Concrete\Core\Site\Locale\Listener"})
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
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Localization\Locale\LocaleInterface::getLocaleID()
     */
    public function getLocaleID()
    {
        return $this->siteLocaleID;
    }

    /**
     * Is this the default locale?
     *
     * @return bool
     */
    public function getIsDefault()
    {
        return $this->msIsDefault;
    }

    /**
     * Is this the default locale?
     *
     * @param bool $msIsDefault
     */
    public function setIsDefault($msIsDefault)
    {
        $this->msIsDefault = (bool) $msIsDefault;
    }

    /**
     * Get the site associated to this locale.
     *
     * @return Site|null
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * Set the site associated to this locale.
     *
     * @param Site|null $site
     */
    public function setSite(Site $site = null)
    {
        $this->site = $site;
    }

    /**
     * Get the site tree associated to this locale.
     *
     * @return SiteTree|null
     */
    public function getSiteTree()
    {
        return $this->tree;
    }

    /**
     * Set the site tree associated to this locale.
     *
     * @param SiteTree|null $tree
     */
    public function setSiteTree(SiteTree $tree = null)
    {
        $this->tree = $tree;
    }

    /**
     * {@inheritdoc}
     *
     * @see TreeInterface::getSiteTreeID()
     */
    public function getSiteTreeID()
    {
        $tree = $this->getSiteTree();

        return $tree === null ? null : $tree->getSiteTreeID();
    }

    /**
     * {@inheritdoc}
     *
     * @see TreeInterface::getSiteTreeObject()
     */
    public function getSiteTreeObject()
    {
        return $this->getSiteTree();
    }
}
