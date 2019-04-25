<?php
namespace Concrete\Core\Editor;

use Concrete\Core\User\User;
use Concrete\Core\Support\Facade\Application;

class UserNameSnippet extends Snippet
{
    public function replace()
    {
        $u = Application::getFacadeApplication()->make(User::class);

        return $u->getUserName();
    }
}
