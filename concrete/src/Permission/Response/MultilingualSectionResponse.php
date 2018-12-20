<?php
namespace Concrete\Core\Permission\Response;

use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\User;

defined('C5_EXECUTE') or die("Access Denied.");

class MultilingualSectionResponse extends PageResponse
{
    public function canImportMultilingualSection()
    {
        $u = Application::getFacadeApplication()->make(User::class);

        return $u->isSuperUser();
    }
}
