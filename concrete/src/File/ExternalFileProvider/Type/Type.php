<?php

namespace Concrete\Core\File\ExternalFileProvider\Type;

use Concrete\Core\Entity\Package as PackageEntity;
use Concrete\Core\Package\Package;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Entity\File\ExternalFileProvider\Type\Type as TypeEntity;
use Doctrine\ORM\EntityManagerInterface;

class Type
{

    /**
     * @param string $efpTypeHandle
     * @param string $efpTypeName
     * @param bool|Package|PackageEntity $pkg
     *
     * @return TypeEntity
     */
    public static function add(string $efpTypeHandle, string $efpTypeName, $pkg)
    {
        $app = Application::getFacadeApplication();
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $app->make(EntityManagerInterface::class);

        $o = new TypeEntity();
        $o->efpTypeHandle = $efpTypeHandle;
        $o->efpTypeName = $efpTypeName;

        if ($pkg instanceof Package || $pkg instanceof PackageEntity) {
            $o->pkgID = $pkg->getPackageID();
        }

        $entityManager->persist($o);
        $entityManager->flush();

        return $o;
    }

    /**
     * @param int $id
     *
     * @return null|TypeEntity|object
     */
    public static function getByID(int $id)
    {
        $app = Application::getFacadeApplication();
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $app->make(EntityManagerInterface::class);
        return $entityManager->getRepository(TypeEntity::class)->findOneBy(
            ['efpTypeID' => $id]
        );
    }

    /**
     * @param $efpTypeHandle
     *
     * @return TypeEntity|object
     */
    public static function getByHandle($efpTypeHandle)
    {
        $app = Application::getFacadeApplication();
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $app->make(EntityManagerInterface::class);
        return $entityManager->getRepository(TypeEntity::class)->findOneBy(
            ['efpTypeHandle' => $efpTypeHandle]
        );
    }

    /**
     * Returns an array of \Concrete\Core\Entity\File\ExternalFileProvider\Type\Type objects.
     *
     * @return TypeEntity[]|object[]
     */
    public static function getList()
    {
        $app = Application::getFacadeApplication();
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $app->make(EntityManagerInterface::class);
        return $entityManager->getRepository(TypeEntity::class)->findBy(
            [],
            [
                'efpTypeID' => 'asc'
            ]
        );
    }

    /**
     * Return an array of Types that are associated with a specific package.
     *
     * @param PackageEntity $pkg
     *
     * @return TypeEntity[]|object[]
     */
    public static function getListByPackage(PackageEntity $pkg)
    {
        $app = Application::getFacadeApplication();
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $app->make(EntityManagerInterface::class);
        return $entityManager->getRepository(TypeEntity::class)->findBy(
            [
                'pkgID' => $pkg->getPackageID()
            ],
            [
                'efpTypeID' => 'asc'
            ]
        );
    }

}
