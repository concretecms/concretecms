<?php
namespace Concrete\Core\Localization\Translation;

use Concrete\Core\Package\Package;

class PackageLocaleStatus extends LocaleStatus
{
    /**
     * @var Package
     */
    protected $package;

    /**
     * @param Package $package
     */
    public function __construct(Package $package)
    {
        $this->package = $package;
        parent::__construct();
    }

    /**
     * @return Package
     */
    public function getPackage()
    {
        return $this->package;
    }
}
