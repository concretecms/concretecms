<?php
namespace Concrete\Core\Export\Item;

use Concrete\Core\Page\Exporter;
use Concrete\Core\Page\PageList;
use Concrete\Core\Page\Stack\StackList;
use Concrete\Core\Page\Template;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Attribute\Category\SiteTypeCategory;
use Concrete\Core\Entity\Site\Skeleton;
use Concrete\Core\Entity\Site\SkeletonLocale;
use Concrete\Core\Site\Type\Skeleton\Service;
use Concrete\Core\Site\User\Group\Service as GroupService;

class SiteType implements ItemInterface
{

    /**
     * @var Service
     */
    protected $skeletonService;

    /**
     * @var SiteTypeCategory
     */
    protected $siteTypeCategory;

    /**
     * @var GroupService
     */
    protected $groupService;

    public function __construct(
        SiteTypeCategory $siteTypeCategory,
        Service $skeletonService,
        GroupService $groupService)
    {
        $this->siteTypeCategory = $siteTypeCategory;
        $this->skeletonService = $skeletonService;
        $this->groupService = $groupService;
    }

    /**
     * @param $type \Concrete\Core\Entity\Site\Type
     * @param \SimpleXMLElement $xml
     * @return mixed
     */
    public function export($type, \SimpleXMLElement $xml)
    {
        $theme = Theme::getByID($type->getSiteTypeThemeID());
        $template = Template::getByID($type->getSiteTypeHomePageTemplateID());
        $sitetype = $xml->addChild('sitetype');
        $sitetype->addAttribute('name', $type->getSiteTypeName());
        $sitetype->addAttribute('handle', $type->getSiteTypeHandle());
        if ($theme) {
            $sitetype->addAttribute('theme', $theme->getThemeHandle());
        }
        if ($template) {
            $sitetype->addAttribute('home-template', $template->getPageTemplateHandle());
        }
        if (is_object($type->getPackage())) {
            $sitetype->addAttribute('package', $type->getPackageHandle());
        }

        /**
         * @var $skeleton Skeleton
         */
        $skeleton = $this->skeletonService->getSkeleton($type);

        // Export attributes
        $values = $this->siteTypeCategory->getAttributeValues($skeleton);
        if (count($values) > 0) {
            $attributes = $sitetype->addChild('attributes');
            foreach ($values as $value) {
                $cnt = $value->getController();
                $akx = $attributes->addChild('attributekey');
                $akx->addAttribute('handle', $value->getAttributeKey()->getAttributeKeyHandle());
                $cnt->exportValue($akx);
            }
        }

        // Export site groups
        $groups = $this->groupService->getSiteTypeGroups($type);
        if (count($groups)) {
            $sitegroups = $sitetype->addChild('sitegroups');
            foreach($groups as $group) {
                $child = $sitegroups->addChild('sitegroup');
                $child->addAttribute('name', $group->getSiteGroupName());
            }
        }

        if (is_object($skeleton)) {
            $skeletonNode = $sitetype->addChild('skeleton');
            foreach($skeleton->getLocales() as $locale) {
                /**
                 * @var $locale SkeletonLocale
                 */
                $localeExporter = $locale->getExporter();
                $skeletonLocalesNode = $localeExporter->export($locale, $skeletonNode);
                $skeletonPagesNode = $skeletonLocalesNode->addChild('pages');

                $list = new PageList();
                $list->setSiteTreeObject($locale->getSiteTree());
                $list->ignorePermissions();
                $list->sortByDisplayOrder();
                $pages = $list->getResults();

                $exporter = new Exporter();

                foreach($pages as $page) {
                    $exporter->export($page, $skeletonPagesNode);
                }

                $stackList = new StackList();
                $stackList->setSiteTreeObject($locale->getSiteTree());
                $stacks = $stackList->getResults();

                if (count($stacks)) {
                    $skeletonStacksNode = $skeletonNode->addChild('stacks');
                    foreach($stacks as $stack) {
                        $exporter->export($stack, $skeletonStacksNode);
                    }
                }

            }
        }
        return $sitetype;
    }

}
