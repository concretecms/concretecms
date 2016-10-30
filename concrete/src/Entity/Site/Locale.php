<?php
namespace Concrete\Core\Entity\Site;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="SiteLocales")
 */
class Locale implements \JsonSerializable
{

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
     * @ORM\Column(type="string", length=32)
     */
    protected $msLanguage;

    /**
     * @ORM\Column(type="string", length=32)
     */
    protected $msCountry;

    /**
     * @ORM\Column(type="integer", length=10)
     */
    protected $msNumPlurals = 2;

    /**
     * @ORM\Column(type="string", length=400)
     */
    protected $msPluralRule = "(n != 1)";

    /**
     * @ORM\Column(type="string", length=1000)
     */
    protected $msPluralCases = "one@1\nother@0, 2~16, 100, 1000, 10000, 100000, 1000000, …";

    /**
     * @return mixed
     */
    public function getSiteLocaleID()
    {
        return $this->siteLocaleID;
    }

    /**
     * @return mixed
     */
    public function getLanguage()
    {
        return $this->msLanguage;
    }

    /**
     * @param mixed $msLanguage
     */
    public function setLanguage($msLanguage)
    {
        $this->msLanguage = $msLanguage;
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->msCountry;
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
     * @param mixed $msCountry
     */
    public function setCountry($msCountry)
    {
        $this->msCountry = $msCountry;
    }

    /**
     * @return mixed
     */
    public function getNumPlurals()
    {
        return $this->msNumPlurals;
    }

    /**
     * @param mixed $msNumPlurals
     */
    public function setNumPlurals($msNumPlurals)
    {
        $this->msNumPlurals = $msNumPlurals;
    }

    /**
     * @return mixed
     */
    public function getPluralRule()
    {
        return $this->msPluralRule;
    }

    /**
     * @param mixed $msPluralRule
     */
    public function setPluralRule($msPluralRule)
    {
        $this->msPluralRule = $msPluralRule;
    }

    /**
     * @return mixed
     */
    public function getPluralCases()
    {
        $msPluralCases = array();
        foreach (explode("\n", $this->msPluralCases) as $line) {
            list($key, $examples) = explode('@', $line);
            $msPluralCases[$key] = $examples;
        }
        return $msPluralCases;
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
     * @param mixed $msPluralCases
     */
    public function setPluralCases($msPluralCases)
    {
        $this->msPluralCases = $msPluralCases;
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

    public function getLocale()
    {
        return sprintf('%s_%s', $this->getLanguage(), $this->getCountry());
    }

    public function getLanguageText($locale = null)
    {
        try {
            if (!$locale) {
                $locale = \Localization::activeLocale();
            }
            $text = \Punic\Language::getName($this->getLanguage(), $locale);
        } catch (\Exception $e) {
            $text = $this->getLanguage();
        }

        return $text;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->getSiteLocaleID(),
            'locale' => $this->getLocale()
        ];
    }

}
