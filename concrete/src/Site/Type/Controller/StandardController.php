<?php
namespace Concrete\Core\Site\Type\Controller;

use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Entity\Site\Type;
use Concrete\Core\Permission\Registry\Applier;
use Concrete\Core\Permission\Registry\ObjectAssignment;
use PortlandLabs\Liberta\Permissions\Registry\Access\DefaultHomePageAccessRegistry;
use Concrete\Core\Site\Type\Formatter\DefaultFormatter;
use Concrete\Core\Site\Type\Skeleton\Service;
use Symfony\Component\HttpFoundation\Request;

class StandardController implements ControllerInterface
{

    protected $permissionsApplier;
    protected $skeletonService;

    public function __construct(Applier $permissionsApplier, Service $skeletonService)
    {
        $this->permissionsApplier = $permissionsApplier;
        $this->skeletonService = $skeletonService;
    }

    public function add(Site $site, Request $request)
    {
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
        return $type;

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
