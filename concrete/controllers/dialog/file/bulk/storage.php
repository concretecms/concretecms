<?php
namespace Concrete\Controller\Dialog\File\Bulk;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\File\EditResponse;
use Concrete\Core\File\File;
use Concrete\Core\File\Set\Set;
use Concrete\Core\File\StorageLocation\StorageLocationFactory;
use Concrete\Core\Foundation\Queue\QueueService;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\View\View;
use Permissions;
use Exception;
use stdClass;

class Storage extends BackendInterfaceController
{
    public $helpers = array('form');
    protected $viewPath = '/dialogs/file/bulk/storage';
    protected $files = array();
    protected $canEdit = false;

    public function on_start()
    {
        parent::on_start();
        $this->populateFiles();
    }

    protected function canAccess()
    {
        return $this->canEdit;
    }

    protected function populateFiles()
    {
        if (is_array($_REQUEST['fID'])) {
            foreach ($_REQUEST['fID'] as $fID) {
                $f = File::getByID($fID);
                if (is_object($f)) {
                    $this->files[] = $f;
                }
            }
        }

        if (count($this->files) > 0) {
            $this->canEdit = true;
            foreach ($this->files as $f) {
                $fp = new Permissions($f);
                if (!$fp->canAdmin()) {
                    $this->canEdit = false;
                }
            }
        } else {
            $this->canEdit = false;
        }

        return $this->canEdit;
    }

    public function view()
    {
        $locations = $this->app->make(StorageLocationFactory::class)->fetchList();
        $this->set('files', $this->files);
        $this->set('locations', $locations);
    }

    public function submit()
    {
        $json = new \Concrete\Core\Application\EditResponse();
        $err = $this->app->make('error');
        if ($this->validateAction()) {
            $post = $this->request->request->all();
            $fsl = $this->app->make(StorageLocationFactory::class)->fetchByID($post['fslID']);
            if (!is_object($fsl)) {
                $err->add(t('Please select valid file storage location.'));
            }
        }
        $json->setError($err);
        return $json->outputJSON();
    }

    public function doChangeStorageLocation()
    {
        if ($this->validateAction()) {
            $post = $this->request->request->all();
            $fsl = $this->app->make(StorageLocationFactory::class)->fetchByID($post['fslID']);
            if (is_object($fsl)) {
                $fIDs = $post['fID'];
                $q = $this->app->make(QueueService::class)->get('change_files_storage_location');
                if ($this->request->request->get('process')) {
                    $obj = new stdClass();
                    $messages = $q->receive(5);
                    foreach ($messages as $key => $msg) {
                        $fID = $msg->body;
                        if ($fID !== false) {
                            $file = File::getByID($fID);
                            if (is_object($file)) {
                                $fp = new Permissions($file);
                                if ($fp->canEditFilePermissions()) {
                                    $file->setFileStorageLocation($fsl);
                                }
                            }
                        }
                        $q->deleteMessage($msg);
                    }
                    $obj->totalItems = $q->count();
                    $obj->files = $fIDs;
                    if ($q->count() == 0) {
                        $q->deleteQueue();
                    }

                    return $this->app->make(ResponseFactoryInterface::class)->json($obj);
                } elseif ($q->count() == 0) {
                    foreach ($fIDs as $fID) {
                        $q->send($fID);
                    }
                }

                $totalItems = $q->count();
                View::element('progress_bar', ['totalItems' => $totalItems, 'totalItemsSummary' => t2('%d file', '%d files', $totalItems)]);
            }
        }
    }
}
