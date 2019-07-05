<?php
namespace Concrete\Core\Application\UserInterface\Sitemap;

use Concrete\Core\Application\Application;
use Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\Entry\LocaleEntry;
use Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\StandardTreeCollection;
use Concrete\Core\Cookie\CookieJar;
use Concrete\Core\Entity\Site\Tree;
use Concrete\Core\Entity\Site\Type;
use Concrete\Core\Http\Request;
use Concrete\Core\Site\Service;
use Concrete\Core\Entity\Site\Skeleton;
use Concrete\Core\Site\Type\Skeleton\Service as SkeletonService;

class SkeletonSitemapProvider extends StandardSitemapProvider
{

    protected $siteType;
    protected $skeletonService;

    public function __construct(
        SkeletonService $skeletonService,
        Type $siteType,
        Application $app,
        CookieJar $cookies,
        Service $siteService,
        Request $request
    )
    {
        $this->siteType = $siteType;
        $this->skeletonService = $skeletonService;
        parent::__construct($app, $cookies, $siteService, $request);
    }

    public function getTreeCollection(Tree $selectedTree = null)
    {
        $collection = new StandardTreeCollection();

        $skeleton = $this->skeletonService->getSkeleton($this->siteType);

        /**
         * @var $skeleton Skeleton
         */
        if (count($skeleton->getLocales()) > 1) {
            foreach($skeleton->getLocales() as $locale) {
                if ($this->checkPermissions($locale)) {
                    $entry = new LocaleEntry($locale);
                    if ($selectedTree && $entry->getSiteTreeID() == $selectedTree->getSiteTreeID()){
                        $entry->setIsSelected(true);
                    }
                    $collection->addEntry($entry);
                }
            }
        }

        return $collection;
    }

    public function getRequestedSiteTree()
    {
        if ($this->request->query->has('siteTreeID') && $this->request->query->get('siteTreeID') > 0) {
            return parent::getRequestedSiteTree();
        } else {
            $skeleton = $this->skeletonService->getSkeleton($this->siteType);
            return $skeleton->getLocales()->get(0)->getSiteTree();
        }
    }

}
