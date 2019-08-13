<?php
namespace Concrete\Core\Support\Facade;

/**
 * @since 8.0.0
 */
class StackFolder extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'Concrete\Core\Page\Stack\Folder\FolderService';
    }

}
