<?php

namespace Concrete\Core\User\Command;

use Concrete\Core\Foundation\Command\Command;
use Concrete\Core\User\UserInfo;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UpdateUserAvatarCommand extends Command
{

    /**
     * @var UserInfo
     */
    protected $user;

    /**
     * @var UploadedFile
     */
    protected $avatarFile;

    /**
     * UpdateUserAvatarCommand constructor.
     * @param UserInfo $user
     * @param UploadedFile $avatarFile
     */
    public function __construct(UserInfo $user, UploadedFile $avatarFile)
    {
        $this->user = $user;
        $this->avatarFile = $avatarFile;
    }

    /**
     * @return UserInfo
     */
    public function getUser(): UserInfo
    {
        return $this->user;
    }

    /**
     * @param UserInfo $user
     */
    public function setUser(UserInfo $user): void
    {
        $this->user = $user;
    }

    /**
     * @return UploadedFile
     */
    public function getAvatarFile(): UploadedFile
    {
        return $this->avatarFile;
    }

    /**
     * @param UploadedFile $avatarFile
     */
    public function setAvatarFile(UploadedFile $avatarFile): void
    {
        $this->avatarFile = $avatarFile;
    }



}
