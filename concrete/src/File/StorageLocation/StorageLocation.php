<?php
namespace Concrete\Core\File\StorageLocation;

use Concrete\Core\File\StorageLocation\Configuration\ConfigurationInterface;
use Database;
use Doctrine\ORM\Mapping as ORM;

class StorageLocation
{

    public static function add(ConfigurationInterface $configuration, $fslName, $fslIsDefault = false)
    {
        $default = self::getDefault();

        $em = \ORM::entityManager();
        $o = new \Concrete\Core\Entity\File\StorageLocation\StorageLocation();
        $o->setName($fslName);
        $o->setIsDefault($fslIsDefault);
        $o->setConfigurationObject($configuration);
        $em->persist($o);

        if ($fslIsDefault && is_object($default)) {
            $default->setIsDefault(false);
            $em->persist($default);
        }

        $em->flush();

        return $o;
    }

    /**
     * @param int $id
     * @return null|StorageLocation
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public static function getByID($id)
    {
        $em = \ORM::entityManager();
        $r = $em->find('\Concrete\Core\Entity\File\StorageLocation\StorageLocation', intval($id));

        return $r;
    }

    /**
     * @return StorageLocation[]
     */
    public static function getList()
    {
        $em = \ORM::entityManager();
        return $em->getRepository('\Concrete\Core\Entity\File\StorageLocation\StorageLocation')->findBy(
            array(), array('fslID' => 'asc')
        );
    }

    /**
     * @return StorageLocation
     */
    public static function getDefault()
    {
        $em = \ORM::entityManager();
        $location = $em->getRepository('\Concrete\Core\Entity\File\StorageLocation\StorageLocation')->findOneBy(
            array('fslIsDefault' => true,
            ));

        return $location;
    }


}
