<?php
namespace Concrete\Core\Entity;

/**
 * @Entity
 * @Table(name="TestPackages")
 */
class Package
{
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $pkgID;

    /**
     * @Column(type="string")
     */
    protected $handle;

    /**
     * @param mixed $pkgID
     */
    public function setPackageID($pkgID)
    {
        $this->pkgID = $pkgID;
    }


    /**
     * @return mixed
     */
    public function getPackageHandle()
    {
        return $this->handle;
    }

    /**
     * @param mixed $handle
     */
    public function setPackageHandle($handle)
    {
        $this->handle = $handle;
    }

    /**
     * @return mixed
     */
    public function getPackageID()
    {
        return $this->pkgID;
    }
}
