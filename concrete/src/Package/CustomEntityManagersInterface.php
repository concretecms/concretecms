<?php
namespace Concrete\Core\Package;

/**
 * Package controllers that implements this interface provide their own EntityMangerInterface instance with the getCustomPackageEntityManagers method.
 */
interface CustomEntityManagersInterface
{
    /**
     * Return custom package entity managers.
     *
     * @return \Doctrine\ORM\EntityManagerInterface[]
     */
    public function getCustomPackageEntityManagers();
}
