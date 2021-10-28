<?php

namespace Concrete\Core\Page\Container;

use Concrete\Core\Filesystem\Icon\AbstractIconRepository;

/**
 * Class IconRepository
 * 
 * Responsible for retrieving a list of icon objects representing area containers.
 */
class IconRepository extends AbstractIconRepository
{
    
    public function getPath()
    {
        return DIR_BASE_CORE .
            DIRECTORY_SEPARATOR .
            DIRNAME_IMAGES .
            DIRECTORY_SEPARATOR .
            'icons' .
            DIRECTORY_SEPARATOR .
            'containers';
    }

    public function getBaseUrl()
    {
        return ASSETS_URL_IMAGES . '/icons/containers/';
    }

}
