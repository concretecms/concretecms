<?php
namespace Concrete\Core\Support\Facade;

class StackFolder extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'Concrete\Core\Page\Stack\Folder\FolderService';
    }

}
