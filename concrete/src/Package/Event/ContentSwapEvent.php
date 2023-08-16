<?php
namespace Concrete\Core\Package\Event;

use Concrete\Core\Package\Package;

class ContentSwapEvent
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
    }

    /**
     * @return Package
     */
    public function getPackage(): Package
    {
        return $this->package;
    }



}
