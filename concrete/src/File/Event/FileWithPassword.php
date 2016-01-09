<?php
namespace Concrete\Core\File\Event;

class FileWithPassword extends File
{

    protected $password;

    public function setFilePassword($password)
    {
        $this->password = $password;
    }

    public function getFilePassword()
    {
        return $this->password;
    }

}
