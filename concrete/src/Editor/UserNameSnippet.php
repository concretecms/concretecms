<?php
namespace Concrete\Core\Editor;

use Concrete\Core\User\User;

class UserNameSnippet extends Snippet
{
    public function replace()
    {
        $u = new User();

        return $u->getUserName();
    }
}
