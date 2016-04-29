<?php
namespace Concrete\Core\Search\Field;

class Manager
{

    public static function get($manager)
    {
        $app = \Core::make('app');
        return $app->make(sprintf('manager/search_field/%s', $manager));
    }
}
