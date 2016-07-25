<?php
namespace Concrete\Core\Page\Type\Saver;

use Concrete\Core\Support\Manager as CoreManager;

class Manager extends CoreManager
{

    protected function getStandardSaver()
    {
        return new StandardSaver();
    }

    public function driver($driver = null)
    {
        // If a custom driver is not registered for our page type validator, we return the default.
        if (!isset($this->customCreators[$driver]) && !isset($this->drivers[$driver])) {
            return $this->getStandardSaver();
        }

        return parent::driver($driver);
    }
}
