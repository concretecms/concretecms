<?php
namespace Concrete\Core\File\StorageLocation\Type;

use Database;
use Core;
use Environment;
use Doctrine\ORM\Mapping as ORM;

class Type
{

    /**
     * @param string $fslTypeHandle
     * @param string $fslTypeName
     * @param int|\Package $pkg
     *
     * @return \Concrete\Core\Entity\File\StorageLocation\Type\Type
     */
    public static function add($fslTypeHandle, $fslTypeName, $pkg = false)
    {
        $em = \ORM::entityManager();
        $o = new \Concrete\Core\Entity\File\StorageLocation\Type\Type();
        $o->fslTypeHandle = $fslTypeHandle;
        $o->fslTypeName = $fslTypeName;
        if ($pkg instanceof \Concrete\Core\Package\Package || $pkg instanceof \Concrete\Core\Entity\Package) {
            $o->pkgID = $pkg->getPackageID();
        }
        $em->persist($o);
        $em->flush();

        return $o;
    }

    /**
     * @param int $id
     *
     * @return null|\Concrete\Core\Entity\File\StorageLocation\Type\Type
     */
    public static function getByID($id)
    {
        $em = \ORM::entityManager();
        $r = $em->find('\Concrete\Core\Entity\File\StorageLocation\Type\Type', $id);

        return $r;
    }

    /**
     * @param $fslTypeHandle
     *
     * @return \Concrete\Core\Entity\File\StorageLocation\Type\Type
     */
    public static function getByHandle($fslTypeHandle)
    {
        $em = \ORM::entityManager();
        $type = $em->getRepository('\Concrete\Core\Entity\File\StorageLocation\Type\Type')->findOneBy(
            array('fslTypeHandle' => $fslTypeHandle,
         ));

        return $type;
    }

    /**
     * Returns an array of \Concrete\Core\Entity\File\StorageLocation\Type\Type objects.
     *
     * @return \Concrete\Core\Entity\File\StorageLocation\Type\Type[]
     */
    public static function getList()
    {
        $em = \ORM::entityManager();
        return $em->getRepository('\Concrete\Core\Entity\File\StorageLocation\Type\Type')->findBy(
            array(), array('fslTypeID' => 'asc')
        );
    }

    /**
     * Return an array of AuthenticationTypes that are associated with a specific package.
     *
     * @param \Package $pkg
     *
     * @return \Concrete\Core\Entity\File\StorageLocation\Type\Type[]
     */
    public static function getListByPackage($pkg)
    {
        $em = \ORM::entityManager();
        return $em->getRepository('\Concrete\Core\Entity\File\StorageLocation\Type\Type')->findBy(
            array('pkgID' => $pkg->getPackageID()), array('fslTypeID' => 'asc')
        );
    }

}
