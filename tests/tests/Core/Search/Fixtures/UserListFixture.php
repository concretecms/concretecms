<?php
namespace Concrete\Tests\Core\Search\Fixtures;

use Concrete\Core\User\UserList;

class UserListFixture extends UserList
{
    public function splitKeywordsWrapper()
    {
        return call_user_func_array([$this, 'splitKeywords'], func_get_args());
    }
}
