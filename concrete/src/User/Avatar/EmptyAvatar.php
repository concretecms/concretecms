<?php
namespace Concrete\Core\User\Avatar;

use Concrete\Core\Application\Application;

/**
 * @since 5.7.5.4
 */
class EmptyAvatar extends StandardAvatar
{
    public function getPath()
    {
        return $this->application['config']->get('concrete.icons.user_avatar.default');
    }
}
