<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Backup\ContentImporter;
use Concrete\Core\Entity\Site\Type;
use Concrete\Core\Page\Template;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Site\Type\Service as TypeService;
use Concrete\Core\Attribute\Category\SiteTypeCategory;
use Doctrine\ORM\EntityManager;
use Concrete\Core\Entity\Site\Skeleton;
use Concrete\Core\Entity\Site\SkeletonLocale;
use Concrete\Core\Site\Type\Skeleton\Service as SkeletonService;
use Concrete\Core\Site\User\Group\Service as GroupService;

class ImportSiteTypeSkeletonsRoutine extends AbstractRoutine
{

    /**
     * @var SkeletonService
     */
    protected $skeletonService;

    /**
     * @var TypeService
     */
    protected $typeService;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var SiteTypeCategory
     */
    protected $siteTypeCategory;

    /**
     * @var GroupService
     */
    protected $groupService;

    public function __construct(
        SiteTypeCategory $category,
        EntityManager $entityManager,
        SkeletonService $skeletonService,
        TypeService $typeService,
        GroupService $groupService
    )
    {
        $this->siteTypeCategory = $category;
        $this->skeletonService = $skeletonService;
        $this->typeService = $typeService;
        $this->entityManager = $entityManager;
        $this->groupService = $groupService;
    }

    public function getHandle()
    {
        return 'site_type_skeletons';
    }

    public function import(\SimpleXMLElement $sx)
    {
        if (isset($sx->sitetypes)) {
            foreach ($sx->sitetypes->sitetype as $type) {
                $handle = (string) $type['handle'];
                $site_type = $this->typeService->getByHandle($handle);
                if (is_object($site_type)) {
                    /**
                     * @var $site_type Type
                     */
                    $theme = Theme::getByHandle((string) $type['theme']);
                    if (is_object($theme)) {
                        $site_type->setSiteTypeThemeID($theme->getThemeID());
                    }
                    $template = Template::getByHandle((string) $type['home-template']);
                    if (is_object($template)) {
                        $site_type->setSiteTypeHomePageTemplateID($template->getPageTemplateID());
                    }

                    $this->entityManager->persist($site_type);
                    $this->entityManager->flush();

                    if (isset($type->sitegroups)) {
                        foreach($type->sitegroups->sitegroup as $group) {
                            $name = (string) $group['name'];
                            $this->groupService->addGroup($site_type, $name);
                        }
                    }

                    // Create the skeleton.
                    foreach($type->skeleton->locale as $localeNode ) {

                        $locale = new SkeletonLocale();
                        $locale->setCountry((string)$localeNode['country']);
                        $locale->setLanguage((string)$localeNode['language']);

                        /**
                         * @var $skeleton Skeleton
                         */
                        $skeleton = $this->skeletonService->getSkeleton($site_type);

                        if (!is_object($skeleton)) {
                            $skeleton = $this->skeletonService->createSkeleton($site_type, $locale);
                        }
                        $home = $skeleton->getMatchingLocale((string)$localeNode['language'], (string)$localeNode['country'])->getSiteTree()->getSiteHomePageObject();

                        $importer = new ContentImporter();
                        $importer->setHomePage($home);
                        $importer->importXml($type->skeleton->locale);

                    }

                    $controller = $this->typeService->getController($site_type);
                    $controller->addType($site_type);

                    if (isset($type->attributes)) {
                        foreach ($type->attributes->children() as $attr) {
                            $handle = (string) $attr['handle'];
                            $ak = $this->siteTypeCategory->getByHandle($handle);
                            if (is_object($ak)) {
                                $value = $ak->getController()->importValue($attr);
                                $skeleton->setAttribute($handle, $value);
                            }
                        }
                    }

                }
            }
        }
    }

}
