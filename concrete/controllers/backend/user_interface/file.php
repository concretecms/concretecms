<?php
namespace Concrete\Controller\Backend\UserInterface;

use Concrete\Core\Entity\File\File as FileEntity;
use Concrete\Core\File\File as ConcreteFile;
use Loader;
use Permissions;
use Exception;

abstract class File extends \Concrete\Controller\Backend\UserInterface
{
    protected $file;

    public function on_start()
    {
        if (!isset($this->file)) {
            $request = $this->request;
            $fID = Loader::helper('security')->sanitizeInt($request->query->get('fID'));
            if ($fID) {
                $file = ConcreteFile::getByID($fID);
                if (is_object($file) && !$file->isError()) {
                    $this->setFileObject($file);
                } else {
                    throw new Exception(t('Invalid file.'));
                }
            }
        }
    }

    public function setFileObject(FileEntity $f)
    {
        $this->file = $f;
        $this->permissions = new Permissions($this->file);
        $this->set('f', $this->file);
        $this->set('fp', $this->permissions);
    }

    public function getViewObject()
    {
        if ($this->permissions->canViewFileInFileManager()) {
            return parent::getViewObject();
        }
        throw new Exception(t('Access Denied'));
    }

    public function action()
    {
        $url = call_user_func_array('parent::action', func_get_args());
        $url .= '&fID=' . $this->file->getFileID();

        return $url;
    }
}
