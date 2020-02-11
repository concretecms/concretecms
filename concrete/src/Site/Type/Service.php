<?php
namespace Concrete\Core\Site\Type;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\Package;
use Concrete\Core\Entity\Site\SkeletonLocale;
use Concrete\Core\Entity\Site\Type;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Concrete\Core\Site\Type\Skeleton\Service as SkeletonService;
use Concrete\Core\Site\User\Group\Service as GroupService;
use Concrete\Core\Site\Type\Controller\Manager as ControllerManager;

class Service
{

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var SkeletonService
     */
    protected $skeletonService;

    /**
     * @var GroupService
     */
    protected $groupService;

    /**
     * @var Factory
     */
    protected $siteTypeFactory;

    /**
     * @var ControllerManager
     */
    protected $siteTypeControllerManager;

    public function __construct(
        Application $application,
        EntityManagerInterface $entityManager,
        SkeletonService $skeletonService,
        GroupService $groupService,
        Factory $siteTypeFactory,
        ControllerManager $siteTypeControllerManager
    )
    {
        $this->entityManager = $entityManager;
        $this->groupService = $groupService;
        $this->skeletonService = $skeletonService;
        $this->app = $application;
        $this->siteTypeFactory = $siteTypeFactory;
        $this->siteTypeControllerManager = $siteTypeControllerManager;
    }

    /**
     * @return SkeletonService
     */
    public function getSkeletonService()
    {
        return $this->skeletonService;
    }

    /**
     * @return GroupService
     */
    public function getGroupService()
    {
        return $this->groupService;
    }

    /**
     * @return Type
     */
    public function getDefault()
    {
        return $this->getByID(1);
    }

    /**
     * @return Type
     */
    public function getByID($typeID)
    {
        return $this->entityManager->getRepository('Concrete\Core\Entity\Site\Type')
            ->findOneBy(array('siteTypeID' => $typeID));
    }

    /**
     * @return Type
     */
    public function getByHandle($typeHandle)
    {
        return $this->entityManager->getRepository('Concrete\Core\Entity\Site\Type')
            ->findOneBy(array('siteTypeHandle' => $typeHandle));
    }

    /**
     * @return Type[]
     */
    public function getByPackage(Package $package)
    {
        return $this->entityManager->getRepository('Concrete\Core\Entity\Site\Type')
            ->findByPackage($package);
    }

    /**
     * @return Type[]
     */
    public function getList()
    {
        return $this->entityManager->getRepository('Concrete\Core\Entity\Site\Type')
            ->findAll();
    }

    /**
     * @return Type[]
     */
    public function getUserAddedList()
    {
        $criteria = new Criteria();
        $criteria->where(Criteria::expr()->neq('siteTypeHandle', 'default'));
        return $this->entityManager->getRepository('Concrete\Core\Entity\Site\Type')
            ->matching($criteria);
    }

    public function delete(Type $type)
    {
        $skeleton = $this->skeletonService->getSkeleton($type);
        if (is_object($skeleton)) {
            $this->skeletonService->delete($skeleton);
        }

        foreach($this->groupService->getSiteTypeGroups($type) as $group) {
            $this->entityManager->remove($group);
        }

        $this->entityManager->flush();

        $this->entityManager->remove($type);
        $this->entityManager->flush();
    }

    public function import($handle, $name, Package $pkg = null)
    {
        return $this->createAndPersistType($handle, $name, $pkg);
    }

    protected function createAndPersistType($handle, $name, Package $pkg = null)
    {
        $type = $this->siteTypeFactory->createEntity();
        $type->setSiteTypeHandle($handle);
        $type->setSiteTypeName($name);
        $type->setPackage($pkg);

        $this->entityManager->persist($type);
        $this->entityManager->flush();

        return $type;
    }

    public function getController(Type $type)
    {
        return $this->siteTypeControllerManager->driver($type->getSiteTypeHandle());
    }

    public function add($handle, $name, Package $pkg = null)
    {
        $type = $this->createAndPersistType($handle, $name, $pkg);
        $locale = new SkeletonLocale();
        $locale->setLanguage('en');
        $locale->setCountry('US');
        $this->skeletonService->createSkeleton($type, $locale);
        $controller = $this->getController($type);
        $type = $controller->addType($type);
        return $type;
    }

    public function installDefault()
    {
        $type = $this->siteTypeFactory->createDefaultEntity();
        $this->entityManager->persist($type);
        $this->entityManager->flush();

        return $type;
    }


}
