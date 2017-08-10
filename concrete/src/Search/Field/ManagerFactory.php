<?php
namespace Concrete\Core\Search\Field;

use Concrete\Core\Support\Facade\Application;

class ManagerFactory
{
    /**
     * Get a search field manager given its handle.
     *
     * @param string $manager the manager handle
     *
     * @return \Concrete\Core\Search\Field\Manager
     */
    public static function get($manager)
    {
        $app = Application::getFacadeApplication();

        return $app->make(sprintf('manager/search_field/%s', $manager));
    }
}
