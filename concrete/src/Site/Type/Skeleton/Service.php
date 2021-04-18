<?php
namespace Concrete\Core\Site\Type\Skeleton;

use Concrete\Core\Application\Application;
use Concrete\Core\Backup\ContentImporter;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Entity\Site\SiteTree;
use Concrete\Core\Entity\Site\Type;
use Doctrine\ORM\EntityManagerInterface;
use Concrete\Core\Entity\Site\Skeleton;
use Concrete\Core\Entity\Site\SkeletonLocale;
use Concrete\Core\Entity\Site\SkeletonTree;
use Concrete\Core\Localization\Locale\Service as LocaleService;
use Concrete\Core\Attribute\Category\SiteTypeCategory;

class Service
{

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var Application
     */
    protected $application;

    /**
     * @var LocaleService
     */
    protected $localeService;

    /**
     * @var SiteTypeCategory
     */
    protected $siteTypeCategory;

    public function __construct(
        Application $application,
        EntityManagerInterface $entityManager,
        LocaleService $localeService,
        SiteTypeCategory $siteTypeCategory
    )
    {
        $this->application = $application;
        $this->localeService = $localeService;
        $this->entityManager = $entityManager;
        $this->siteTypeCategory = $siteTypeCategory;
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    public function getSkeleton(Type $type)
    {
        $r = $this->entityManager->getRepository(Skeleton::class);
        return $r->findOneByType($type);
    }

    public function getHomePage(Type $type)
    {
        /**
         * @var $skeleton Skeleton
         */
        $skeleton = $this->getSkeleton($type);
        $tree = $skeleton->getLocales()[0]->getSiteTree();
        if (is_object($tree)) {
            return $tree->getSiteHomePageObject();
        }
    }

    public function getAttributeValues(Skeleton $skeleton)
    {
        return $this->siteTypeCategory->getAttributeValues($skeleton);
    }

    public function createSkeleton(Type $type, SkeletonLocale $locale)
    {
        // Create a new site tree for the type
        $tree = new SkeletonTree();
        $tree->setType($type);

        $this->entityManager->persist($tree);
        $this->entityManager->flush();

        $home = \Page::addHomePage($tree);

        // We have to update the site tree immediately because subsequent attempts to Page::isHomePage
        // rely on the site home page ID being set in the $tree object
        $tree->setSiteHomePageID($home->getCollectionID());
        $this->entityManager->persist($tree);
        $this->entityManager->flush();

        $name = t('Skeleton Home: %s', $type->getSiteTypeName());
        $home->update([
            'cName' => $name,
            'pTemplateID' => $type->getSiteTypeHomePageTemplateID()
        ]);

        // Create skeleton versions of the required root single pages
        $importer = new ContentImporter();
        $importer->setHomePage($home);
        $importer->importContentFile(DIR_BASE_CORE . '/config/install/base/single_pages/root.xml');

        $skeleton = new Skeleton();
        $skeleton->setType($type);

        $this->entityManager->persist($skeleton);
        $this->entityManager->flush();

        // Now create a default locale:
        $locale->setSkeleton($skeleton);
        $tree->setLocale($locale);
        $locale->setSiteTree($tree);
        $locale = $this->localeService->updatePluralSettings($locale);

        $this->entityManager->persist($locale);
        $this->entityManager->persist($tree);
        $this->entityManager->persist($skeleton);
        $this->entityManager->flush();

        $this->entityManager->refresh($skeleton);

        return $skeleton;
    }

    public function delete(Skeleton $skeleton)
    {
        $locales = $skeleton->getLocales();
        foreach($locales as $locale) {
            $this->localeService->delete($locale);
        }

        $this->entityManager->remove($skeleton);
        $this->entityManager->flush();
    }

    public function publishSkeletonToSite(Skeleton $skeleton, Site $site)
    {
        // First, create a new site tree
        $tree = new SiteTree();
        $this->entityManager->persist($tree);
        $this->entityManager->flush();

        $locale = $site->getDefaultLocale();

        $tree->setLocale($locale);
        $locale->setSiteTree($tree);

        $this->entityManager->persist($locale);
        $this->entityManager->persist($tree);
        $this->entityManager->flush();

        // Duplicate the home page.
        $skeletonHome = $skeleton->getLocales()[0]->getSiteTree()->getSiteHomePageObject();
        $home = $skeletonHome->duplicateAll(null, true, $site);

        $tree->setSiteHomePageID($home->getCollectionID());
        $this->entityManager->persist($tree);
        $this->entityManager->flush();

        // This is stupid, but the handle has been incorrectly set because when we duplicated the page
        // we didn't know if this was the home page or not.
        $home->update(['cName' => $site->getSiteName(), 'cHandle' => '']);
        $home->rescanCollectionPath(); // Have to do this so we clear out the isSystemPage boolean.

        // Now duplicate the other top level pages. Duplicate them UNDER home, then move to them root.
        // Kind of goofy, but it helps with permission inheritance
        // Add copies of all the root-level single pages like stacks, page not found

        $db = $this->entityManager->getConnection();
        $siteTreeID = $skeleton->getLocales()[0]->getSiteTree()->getSiteTreeID();
        $r = $db->executeQuery('select cID from Pages where cIsTemplate = 0 and siteTreeID = ? and cParentID = 0 and cID <> ?', [$siteTreeID, $skeletonHome->getCollectionID()]);
        while ($row = $r->fetch()) {
            $c = \Page::getByID($row['cID']);
            $c = $c->duplicateAll($home, false, $site);
            $c->moveToRoot();
        }

        // Finally, delete

        $this->entityManager->persist($site);
        $this->entityManager->flush();

        return $home;
    }

}
