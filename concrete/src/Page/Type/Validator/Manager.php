<?php
namespace Concrete\Core\Page\Type\Validator;

use Concrete\Core\Support\Manager as CoreManager;

/**
 * @since 5.7.4
 */
class Manager extends CoreManager
{

    /**
     * @since 8.0.0
     */
    protected function getStandardValidator()
    {
        return new StandardValidator();
    }

    public function driver($driver = null)
    {
        // If a custom driver is not registered for our page type validator, we return the default.
        if (!isset($this->customCreators[$driver]) && !isset($this->drivers[$driver])) {
            return $this->getStandardValidator();
        }

        return parent::driver($driver);
    }
}
