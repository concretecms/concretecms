<?php
namespace Concrete\Core\Localization\Locale;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Page\Template;
use Concrete\Core\Entity\Site\Locale;
use Concrete\Core\Entity\Site\LocaleEntityInterface;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Entity\Site\SiteTree;
use Concrete\Core\Page\Page;
use Doctrine\DBAL\Exception\TableNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Events;

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

    /**
     * Get the default site locale (if set).
     *
     * @return Locale|null
     */
    public function getDefaultLocale()
    {
        try {
            return $this->entityManager->getRepository(Locale::class)->findOneBy(['msIsDefault' => true]);
        } catch (TableNotFoundException $e) {
            return null;
        }
    }

    public function setDefaultLocale(Locale $newDefaultLocale)
    {
        $db = $this->entityManager->getConnection();
        $site = $newDefaultLocale->getSite();
        $existingDefaultLocale = $site->getDefaultLocale();
        // Now let's find all pages under this locale that have a cParentID of zero. Those are the "root"
        // pages like /!drafts. Each multi-site site has a single drafts page, that lives beneath the
        // default locale. That means if you have multiple sites in your Concrete installation, you have
        // multiple drafts pages. That's fine. However if you have multilingual locales, you only have a single
        // Drafts page per site, located under the default locale. If you we don't move the drafts page with the
        // default locale when we move it, deleting the once-default locale will result in a Concrete site without
        // a drafts page.
        $pageIDsToMove = [];
        $qb = $db->createQueryBuilder();
        $r = $qb->select('cID')
            ->from('Pages')
            ->where('cParentID = 0')
            ->andWhere('siteTreeID = :siteTreeID')
            ->andWhere('cFilename is not null')
            ->setParameter('siteTreeID', $existingDefaultLocale->getSiteTreeID())
            ->execute();
        while ($row = $r->fetchAssociative()) {
            $pageIDsToMove[] = $row['cID'];
        }

        foreach ($newDefaultLocale->getSite()->getLocales() as $locale) {
            $locale->setIsDefault(false);
            $this->entityManager->persist($locale);
        }
        $this->entityManager->flush();
        $newDefaultLocale->setIsDefault(true);
        $this->entityManager->persist($newDefaultLocale);
        $this->entityManager->flush();

        // Now let's move the pages.
        if (count($pageIDsToMove)) {
            $db->createQueryBuilder()
                ->update('Pages', 'p')
                ->set('siteTreeID', $newDefaultLocale->getSiteTreeID())
                ->where('p.cID in (:pageIDsToMove)')
                ->setParameter('pageIDsToMove', $pageIDsToMove, Connection::PARAM_INT_ARRAY)
                ->execute();
        }
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
        
        $event = new \Symfony\Component\EventDispatcher\GenericEvent();
        $event->setArgument('locale', $locale);
        Events::dispatch('on_locale_add', $event);
        
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
        $homeCID = Page::getHomePageID();
        if ($homeCID !== null) {
            $home->acquirePagePermissions($homeCID);
        }

        return $home;
    }

    public function delete(LocaleEntityInterface $locale)
    {
        $tree = $locale->getSiteTree();
        if (is_object($tree)) {
            $home = $tree->getSiteHomePageObject();
            if ($home) {
                $home->delete();
            }
            $locale->setSiteTree(null);
            $this->entityManager->remove($tree);
            $this->entityManager->flush();
        }
        $this->entityManager->remove($locale);
        $this->entityManager->flush();
        
        $event = new \Symfony\Component\EventDispatcher\GenericEvent();
        $event->setArgument('locale', $locale);
        Events::dispatch('on_locale_delete', $event);
    }
}
