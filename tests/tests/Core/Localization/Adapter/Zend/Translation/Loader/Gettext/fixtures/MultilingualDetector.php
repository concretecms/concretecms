<?php

namespace Concrete\Tests\Core\Localization\Translator\Adapter\Zend\Translation\Loader\Gettext\Fixtures;

/**
 * Dummy multilingual detector that allows us to test the site translation
 * loading without the need to have a database connection or import any
 * multilingual sections into the DB.
 *
 * @author Antti Hukkanen <antti.hukkanen@mainiotech.fi>
 */
class MultilingualDetector
{

    public function isEnabled()
    {
        return true;
    }

}
