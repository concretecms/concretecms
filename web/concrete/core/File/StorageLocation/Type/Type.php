<?php
namespace Concrete\Core\File\StorageLocation\Type;
use Database;
use Core;
/**
 * @Entity
 * @Table(name="FileStorageLocationTypes")
 */
class Type
{

    /**
     * @Column(type="text")
     */
    protected $fslTypeHandle;

    /**
     * @Column(type="text")
     */
    protected $fslTypeName;

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    protected $fslTypeID;

    /**
     * @Column(type="integer")
     */
    protected $pkgID = 0;

    public function getHandle()
    {
        return $this->fslTypeHandle;
    }

    public function getID()
    {
        return $this->fslTypeID;
    }

    public function getName()
    {
        return $this->fslTypeName;
    }

    public function getPackageID()
    {
        return $this->pkgID;
    }

    /**
     * @return \Concrete\Core\File\StorageLocation\Configuration\ConfigurationInterface
     */
    public function getConfigurationObject()
    {
        return Core::make('\\Concrete\\Core\\File\\StorageLocation\\Configuration\\'
        . camelcase($this->getHandle()) . 'Configuration');
    }

    /**
     * @param $fslTypeHandle
     * @param $fslTypeName
     * @param int $pkgID
     * @return \Concrete\Core\File\StorageLocation\Type\Type
     */
    public static function add($fslTypeHandle, $fslTypeName, $pkgID = 0)
    {
        $db = Database::get();
        $em = $db->getEntityManager();
        $o = new static();
        $o->fslTypeHandle = $fslTypeHandle;
        $o->fslTypeName = $fslTypeName;
        if ($pkgID > 0) {
            $o->pkgID = $pkgID;
        }
        $em->persist($o);
        $em->flush();
        return $o;
    }

    public static function getByID($id)
    {
        $db = Database::get();
        $em = $db->getEntityManager();
        $r = $em->find('\Concrete\Core\File\StorageLocation\Type', $id);
        return $r;
    }


    /**
     * @param $fslTypeHandle
     * @return \Concrete\Core\File\StorageLocation\Type\Type
     */
    public static function getByHandle($fslTypeHandle)
    {
        $db = Database::get();
        $em = $db->getEntityManager();
        $type = $em->getRepository('\Concrete\Core\File\StorageLocation\Type\Type')->findOneBy(
            array('fslTypeHandle' => $fslTypeHandle
         ));
        return $type;
    }




}