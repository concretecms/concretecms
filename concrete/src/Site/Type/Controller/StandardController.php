<?php
namespace Concrete\Core\Site\Type\Controller;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Entity\Site\Type;
use Concrete\Core\File\Filesystem;
use Concrete\Core\Permission\Registry\Applier;
use Concrete\Core\Permission\Registry\Multisite\Access\DefaultHomePageAccessRegistry;
use Concrete\Core\Permission\Registry\Multisite\Access\SiteFileFolderAccessRegistry;
use Concrete\Core\Permission\Registry\ObjectAssignment;
use Concrete\Core\Site\Type\Formatter\DefaultFormatter;
use Concrete\Core\Site\Type\Skeleton\Service as SkeletonService;
use Concrete\Core\Site\User\Group\Service as GroupService;
use Symfony\Component\HttpFoundation\Request;

class StandardController implements ControllerInterface
{

    /**
     * @var Applier
     */
    protected $permissionsApplier;

    /**
     * @var SkeletonService
     */
    protected $skeletonService;

    /**
     * @var GroupService
     */
    protected $groupService;

    /**
     * @var Application
     */
    protected $app;

    public function __construct(
        Application $app,
        Applier $permissionsApplier,
        SkeletonService $skeletonService,
        GroupService $groupService)
    {
        $this->permissionsApplier = $permissionsApplier;
        $this->app = $app;
        $this->groupService = $groupService;
        $this->skeletonService = $skeletonService;
    }

    protected function addSiteFolder(Site $site, Request $request)
    {
        $filesystem = new Filesystem();
        $root = $filesystem->getRootFolder();
        $folder = $filesystem->addFolder($root, $site->getSiteName());

        // apply the default permissions.
        $this->permissionsApplier->apply(
            new \Concrete\Core\Permission\Registry\Entry\Object\Object\FileFolder($folder),
            new SiteFileFolderAccessRegistry($site, $this->groupService)
        );
    }

    public function add(Site $site, Request $request)
    {
        $this->addSiteFolder($site, $request);
        return $site;
    }

    public function update(Site $site, Request $request)
    {
        return $site;
    }

    public function delete(Site $site)
    {
        return false;
    }

    public function addType(Type $type)
    {
        $home = $this->skeletonService->getHomePage($type);
        $this->permissionsApplier->applyAssignment(
            new ObjectAssignment(
                new \Concrete\Core\Permission\Registry\Entry\Object\Object\Page($home),
                new DefaultHomePageAccessRegistry()
            )
        );
        return $type;
    }

    public function getFormatter(Type $type)
    {
        return new DefaultFormatter($type);
    }
}
