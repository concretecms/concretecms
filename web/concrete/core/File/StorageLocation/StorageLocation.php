<?php
namespace Concrete\Core\File\StorageLocation;
use Concrete\Core\File\StorageLocation\Configuration\Configuration;
use Database;
use Core;
/**
 * @Entity
 * @Table(name="FileStorageLocations")
 */
class StorageLocation
{

    /**
     * @Column(type="text")
     */
    protected $fslName;


    /**
     * @Column(type="object")
     */
    protected $fslConfiguration;

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    protected $fslID;

    public function getID()
    {
        return $this->fslID;
    }

    public function getName()
    {
        return $this->fslName;
    }

    public function getConfigurationObject()
    {
        return $this->fslConfiguration;
    }

    public static function add(Configuration $configuration, $fslName)
    {
        $db = Database::get();
        $em = $db->getEntityManager();
        $o = new static();
        $o->fslName = $fslName;
        $o->fslConfiguration = $configuration;
        $em->persist($o);
        $em->flush();
        return $o;
    }

    public static function getByID($id)
    {
        $db = Database::get();
        $em = $db->getEntityManager();
        $r = $em->find('\Concrete\Core\File\StorageLocation\StorageLocation', $id);
        return $r;
    }




}