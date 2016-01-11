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
     * @return mixed
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * @param mixed $handle
     */
    public function setHandle($handle)
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
