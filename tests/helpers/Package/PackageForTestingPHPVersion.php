<?php

namespace Concrete\TestHelpers\Package;

use Concrete\Core\Package\Package;

class PackageForTestingPHPVersion extends Package
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Package\Package::getPackagePath()
     */
    public function getPackagePath()
    {
        return __DIR__;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Package\Package::getPackageHandle()
     */
    public function getPackageHandle()
    {
        return 'handle';
    }

    public function setPHPVersionRequired(string $value): self
    {
        $this->phpVersionRequired = $value;

        return $this;
    }
}