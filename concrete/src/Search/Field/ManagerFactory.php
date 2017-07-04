<?php
namespace Concrete\Core\Search\Field;

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
        $app = \Core::make('app');

        return $app->make(sprintf('manager/search_field/%s', $manager));
    }
}
