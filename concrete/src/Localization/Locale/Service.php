<?php
namespace Concrete\Core\Localization\Locale;

use Concrete\Core\Entity\Page\Template;
use Concrete\Core\Entity\Site\Locale;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Entity\Site\SiteTree;
use Concrete\Core\Page\Page;
use Doctrine\ORM\EntityManagerInterface;

class Service
{
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getByID($id)
    {
        return $this->entityManager->find('Concrete\Core\Entity\Site\Locale', $id);
    }

    public function setDefaultLocale(Locale $defaultLocale)
    {
        foreach ($defaultLocale->getSite()->getLocales() as $locale) {
            $locale->setIsDefault(false);
            $this->entityManager->persist($locale);
        }
        $this->entityManager->flush();

        $defaultLocale->setIsDefault(true);
        $this->entityManager->persist($defaultLocale);
        $this->entityManager->flush();
    }

    public function add(Site $site, $language, $country)
    {
        $tree = new SiteTree();
        $this->entityManager->persist($tree);
        $this->entityManager->flush();

        $locale = new Locale();
        $locale->setCountry($country);
        $locale->setLanguage($language);
        $locale->setSite($site);
        $tree->setLocale($locale);
        $locale->setSiteTree($tree);
        $locale = $this->updatePluralSettings($locale);
        $this->entityManager->persist($tree);
        $this->entityManager->persist($locale);
        $this->entityManager->flush();

        return $locale;
    }

    public function updatePluralSettings(LocaleInterface $l, $numPlurals = null, $pluralRule = '', $pluralCases = [])
    {
        if (empty($numPlurals) || ($pluralRule === '') || (empty($pluralCases))) {
            $locale = $l->getLocale();
            $localeInfo = \Gettext\Languages\Language::getById($locale);
            if ($localeInfo) {
                $numPlurals = count($localeInfo->categories);
                $pluralRule = $localeInfo->formula;
                $pluralCases = [];
                foreach ($localeInfo->categories as $category) {
                    $pluralCases[] = $category->id . '@' . $category->examples;
                }
                $pluralCases = is_array($pluralCases) ? implode("\n", $pluralCases) : $pluralCases;
                $l->setNumPlurals($numPlurals);
                $l->setPluralCases($pluralCases);
                $l->setPluralRule($pluralRule);
            }
        }

        if ((!empty($numPlurals)) && ($pluralRule !== '') && (!empty($pluralCases))) {
            $l->setPluralRule($pluralRule);
            $l->setNumPlurals($numPlurals);
            $pluralCases = is_array($pluralCases) ? implode("\n", $pluralCases) : $pluralCases;
            $l->setPluralCases($pluralCases);
        }

        return $l;
    }

    public function addHomePage(Locale $locale, Template $template, $name, $url_slug = null)
    {
        $tree = $locale->getSiteTree();
        $home = Page::addHomePage($tree);
        $tree->setLocale($locale);
        $tree->setSiteHomePageID($home->getCollectionID());
        $this->entityManager->persist($tree);
        $this->entityManager->flush();
        $home->update([
            'cName' => $name,
            'pTemplateID' => $template->getPageTemplateID(),
            'cHandle' => $url_slug ? $url_slug : $locale->getLocale(),
        ]);
        $home->rescanCollectionPath();

        // Copy the permissions from the canonical home page to this home page.
        $home->acquirePagePermissions(HOME_CID);

        return $home;
    }

    public function delete(Locale $locale)
    {
        $tree = $locale->getSiteTree();
        if (is_object($tree)) {
            $locale->setSiteTree(null);
            $this->entityManager->remove($tree);
            $this->entityManager->flush();
        }
        $this->entityManager->remove($locale);
        $this->entityManager->flush();
    }
}
