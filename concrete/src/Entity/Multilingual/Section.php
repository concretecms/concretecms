<?php
namespace Concrete\Core\Entity\Multilingual;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="MultilingualSections")
 */
class Section
{

    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned":true})
     */
    protected $cID;

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\Site\Site")
     * @ORM\JoinColumn(name="siteID", referencedColumnName="siteID")
     */
    protected $site;

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
    public function getPageID()
    {
        return $this->cID;
    }

    /**
     * @param mixed $cID
     */
    public function setPageID($cID)
    {
        $this->cID = $cID;
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
     * @param mixed $msPluralCases
     */
    public function setPluralCases($msPluralCases)
    {
        $this->msPluralCases = $msPluralCases;
    }




}
