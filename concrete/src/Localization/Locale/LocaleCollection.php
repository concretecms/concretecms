<?php
namespace Concrete\Core\Localization\Locale;

use Concrete\Core\Entity\Site\Locale;
use Concrete\Core\Entity\Site\SiteTree;
use Concrete\Core\Localization\Service\CountryList;
use Concrete\Core\Multilingual\Service\UserInterface\Flag;
use Doctrine\ORM\EntityManagerInterface;

class LocaleCollection implements \JsonSerializable
{

    protected $flag;
    protected $selectedLocale;
    protected $provider;

    public function __construct(LocaleCollectionProviderInterface $provider)
    {
        $this->flag = new Flag();
        $this->countries = new CountryList();
        $this->provider = $provider;
    }

    protected function getLocaleDisplayName(Locale $locale)
    {
        $name = $locale->getLanguageText();
        foreach($this->getLocales() as $otherLocale) {
            if ($otherLocale->getSiteLocaleID() != $locale->getSiteLocaleID() && $otherLocale->getLanguage () == $locale->getLanguage()) {
                $name = sprintf('%s (%s)', $locale->getLanguageText(), $this->countries->getCountryName($locale->getCountry()));
            }
        }
        return $name;
    }

    /**
     * @param mixed $selectedLocale
     */
    public function setSelectedLocale($selectedLocale)
    {
        $this->selectedLocale = $selectedLocale;
    }

    public function jsonSerialize()
    {
        $locales = array();
        foreach($this->getLocales() as $locale) {
            if (isset($this->selectedLocale)) {
                $selectedLocale = $this->selectedLocale->getSiteLocaleID() == $locale->getSiteLocaleID() ? true : false;
            }
            $locales[] = [
                'id' => $locale->getSiteLocaleID(),
                'locale' => $locale->getLocale(),
                'localeDisplayName' => $this->getLocaleDisplayName($locale),
                'icon' => (string) $this->flag->getLocaleFlagIcon($locale, true),
                'treeID' => $locale->getSiteTree()->getSiteTreeID(),
                'selectedLocale' => $selectedLocale,
            ];
        }
        return $locales;
    }

    public function getLocales()
    {
        return $this->provider->getLocales();
    }

}
