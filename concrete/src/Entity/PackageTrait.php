<?php
namespace Concrete\Core\Entity;

trait PackageTrait
{
    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\Package", cascade={"persist"})
     * @ORM\JoinColumn(name="pkgID", referencedColumnName="pkgID", nullable=true)
     */
    protected $package = null;

    /**
     * @return mixed
     */
    public function getPackage()
    {
        return $this->package;
    }

    /**
     * @param mixed $package
     */
    public function setPackage($package)
    {
        $this->package = $package;
    }

    public function getPackageID()
    {
        if (is_object($this->package)) {
            return $this->package->getPackageID();
        }
    }

    public function getPackageHandle()
    {
        if (is_object($this->package)) {
            return $this->package->getPackageHandle();
        }
    }
}
