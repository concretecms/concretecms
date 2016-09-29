<?php
namespace Concrete\Core\Permission\Response;
use Concrete\Core\File\Filesystem;
use User;
use FileSet;

/**
 * @deprecated
 */
class FileSetResponse extends Response {

    protected $permissions;

    public function __call($nm, $args)
    {
        if (!isset($this->permissions)) {
            $filesystem = new Filesystem();
            $this->permissions = new \Permissions($filesystem->getRootFolder());
        }

        return call_user_func_array(array($this->permissions, $nm), $args);
    }


}
