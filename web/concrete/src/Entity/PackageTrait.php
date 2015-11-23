<?php

namespace Concrete\Core\Entity;

trait PackageTrait
{

    /**
     * @ManyToOne(targetEntity="\Concrete\Core\Entity\Package")
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


}
