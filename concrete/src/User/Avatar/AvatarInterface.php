<?php
namespace Concrete\Core\User\Avatar;

/**
 * @since 5.7.5.4
 */
interface AvatarInterface
{
    public function output();
    public function getPath();
}
