<?php
namespace Concrete\Controller\Dialog\File;

use Concrete\Controller\Backend\UserInterface;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\File\EditResponse;
use Concrete\Core\File\File;
use Concrete\Core\Permission\Checker;
use Symfony\Component\HttpFoundation\JsonResponse;

class Delete extends UserInterface
{

    protected $viewPath = '/dialogs/file/delete';

    protected function canAccess()
    {
        $file = File::getByID($this->request->attributes->get('fID'));
        if (!$file) {
            throw new UserMessageException(t('Invalid file object.'));
        }
        $checker = new Checker($file);
        return $checker->canDeleteFile();
    }

    public function view($fID)
    {
        $file = File::getByID($this->request->attributes->get('fID'));
        $this->set('file', $file);
    }

    public function submit($fID)
    {
        if ($this->canAccess()) {
            $file = File::getByID($this->request->attributes->get('fID'));
            $file->delete();

            $this->flash('success', t('File deleted successfully.'));

            $response = new EditResponse();
            $response->setFile($file);
            return new JsonResponse($response);
        }
        $this->view($fID);
    }

}
