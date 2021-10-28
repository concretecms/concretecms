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
use Concrete\Core\Site\Type\Formatter\FormatterInterface;
use Concrete\Core\Site\Type\Skeleton\Service as SkeletonService;
use Concrete\Core\Site\User\Group\Service as GroupService;
use Concrete\Core\Tree\Node\Type\FileFolder;
use Symfony\Component\HttpFoundation\Request;

class StandardController implements ControllerInterface
{
    /**
     * @var \Concrete\Core\Permission\Registry\Applier
     */
    protected $permissionsApplier;

    /**
     * @var \Concrete\Core\Site\Type\Skeleton\Service
     */
    protected $skeletonService;

    /**
     * @var \Concrete\Core\Site\User\Group\Service
     */
    protected $groupService;

    /**
     * @var \Concrete\Core\Application\Application
     */
    protected $app;

    public function __construct(Application $app, Applier $permissionsApplier, SkeletonService $skeletonService, GroupService $groupService)
    {
        $this->permissionsApplier = $permissionsApplier;
        $this->app = $app;
        $this->groupService = $groupService;
        $this->skeletonService = $skeletonService;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Site\Type\Controller\ControllerInterface::add()
     */
    public function add(Site $site, Request $request): Site
    {
        $this->addSiteFolder($site, $request);

        return $site;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Site\Type\Controller\ControllerInterface::update()
     */
    public function update(Site $site, Request $request): Site
    {
        return $site;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Site\Type\Controller\ControllerInterface::delete()
     */
    public function delete(Site $site): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Site\Type\Controller\ControllerInterface::addType()
     */
    public function addType(Type $type): Type
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

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Site\Type\Controller\ControllerInterface::getFormatter()
     */
    public function getFormatter(Type $type): FormatterInterface
    {
        return new DefaultFormatter($type);
    }

    protected function addSiteFolder(Site $site, Request $request): FileFolder
    {
        $filesystem = new Filesystem();
        $root = $filesystem->getRootFolder();
        $folder = $filesystem->addFolder($root, $site->getSiteName());

        // apply the default permissions.
        $this->permissionsApplier->apply(
            new \Concrete\Core\Permission\Registry\Entry\Object\Object\FileFolder($folder),
            new SiteFileFolderAccessRegistry($site, $this->groupService)
        );

        return $folder;
    }
}
