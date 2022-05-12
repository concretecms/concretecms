<?php

namespace Concrete\Core\Site\Type;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\Package;
use Concrete\Core\Entity\Site\SkeletonLocale;
use Concrete\Core\Entity\Site\Type;
use Concrete\Core\Site\Type\Controller\ControllerInterface;
use Concrete\Core\Site\Type\Controller\Manager as ControllerManager;
use Concrete\Core\Site\Type\Skeleton\Service as SkeletonService;
use Concrete\Core\Site\User\Group\Service as GroupService;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;

class Service
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var \Concrete\Core\Application\Application
     */
    protected $app;

    /**
     * @var \Concrete\Core\Site\Type\Skeleton\Service
     */
    protected $skeletonService;

    /**
     * @var \Concrete\Core\Site\User\Group\Service
     */
    protected $groupService;

    /**
     * @var \Concrete\Core\Site\Type\Factory
     */
    protected $siteTypeFactory;

    /**
     * @var \Concrete\Core\Site\Type\Controller\Manager
     */
    protected $siteTypeControllerManager;

    public function __construct(
        Application $application,
        EntityManagerInterface $entityManager,
        SkeletonService $skeletonService,
        GroupService $groupService,
        Factory $siteTypeFactory,
        ControllerManager $siteTypeControllerManager
    ) {
        $this->entityManager = $entityManager;
        $this->groupService = $groupService;
        $this->skeletonService = $skeletonService;
        $this->app = $application;
        $this->siteTypeFactory = $siteTypeFactory;
        $this->siteTypeControllerManager = $siteTypeControllerManager;
    }

    public function getSkeletonService(): SkeletonService
    {
        return $this->skeletonService;
    }

    public function getGroupService(): GroupService
    {
        return $this->groupService;
    }

    public function getDefault(): ?Type
    {
        return $this->getByHandle('default');
    }

    /**
     * @param int|mixed $typeID
     */
    public function getByID($typeID): ?Type
    {
        $typeID = (int) $typeID;

        return $typeID === 0 ? null : $this->entityManager->getRepository(Type::class)->findOneBy(['siteTypeID' => $typeID]);
    }

    /**
     * @param string|mixed $typeHandle
     */
    public function getByHandle($typeHandle): ?Type
    {
        $typeHandle = (string) $typeHandle;

        return $typeHandle === '' ? null : $this->entityManager->getRepository(Type::class)->findOneBy(['siteTypeHandle' => $typeHandle]);
    }

    /**
     * @return \Concrete\Core\Entity\Site\Type[]
     */
    public function getByPackage(Package $package): array
    {
        return $this->entityManager->getRepository(Type::class)->findByPackage($package);
    }

    /**
     * @return \Concrete\Core\Entity\Site\Type[]
     */
    public function getList(): array
    {
        return $this->entityManager->getRepository(Type::class)->findAll();
    }

    /**
     * @return \Doctrine\Common\Collections\Collection|\Concrete\Core\Entity\Site\Type[]
     */
    public function getUserAddedList(): Collection
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->neq('siteTypeHandle', 'default'))
        ;

        return $this->entityManager->getRepository(Type::class)->matching($criteria)
        ;
    }

    public function delete(Type $type): void
    {
        $skeleton = $this->skeletonService->getSkeleton($type);
        if ($skeleton !== null) {
            $this->skeletonService->delete($skeleton);
        }

        foreach ($this->groupService->getSiteTypeGroups($type) as $group) {
            $this->entityManager->remove($group);
        }

        $this->entityManager->flush();

        $this->entityManager->remove($type);
        $this->entityManager->flush();
    }

    public function import(string $handle, string $name, ?Package $pkg = null): Type
    {
        return $this->createAndPersistType($handle, $name, $pkg);
    }

    public function getController(Type $type): ControllerInterface
    {
        return $this->siteTypeControllerManager->driver($type->getSiteTypeHandle());
    }

    public function add(string $handle, string $name, ?Package $pkg = null): Type
    {
        $type = $this->createAndPersistType($handle, $name, $pkg);
        $locale = new SkeletonLocale();
        $locale->setLanguage('en');
        $locale->setCountry('US');
        $this->skeletonService->createSkeleton($type, $locale);
        $controller = $this->getController($type);

        return $controller->addType($type);
    }

    public function installDefault(): Type
    {
        $type = $this->siteTypeFactory->createDefaultEntity();
        $this->entityManager->persist($type);
        $this->entityManager->flush();

        return $type;
    }

    protected function createAndPersistType(string $handle, string $name, ?Package $pkg = null): Type
    {
        $type = $this->siteTypeFactory->createEntity();
        $type->setSiteTypeHandle($handle);
        $type->setSiteTypeName($name);
        $type->setPackage($pkg);

        $this->entityManager->persist($type);
        $this->entityManager->flush();

        return $type;
    }
}
