<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Files;

use Concrete\Core\Page\Controller\DashboardPageController;
use Loader;
use Concrete\Core\File\StorageLocation\StorageLocation as FileStorageLocation;
use Concrete\Core\File\StorageLocation\Type\Type;

class Storage extends DashboardPageController
{
    public $helpers = array('form', 'concrete/ui', 'validation/token', 'concrete/file');

    public function view($updated = false)
    {
        $this->set('locations', FileStorageLocation::getList());
        $types = array();
        $storageLocationTypes = Type::getList();
        foreach ($storageLocationTypes as $type) {
            if ($type->getHandle() == 'default') {
                continue;
            }
            $types[$type->getID()] = $type->getName();
        }
        $this->set('types', $types);
    }

    public function select_type()
    {
        $fslTypeID = $this->request('fslTypeID');
        $type = Type::getByID($fslTypeID);
        $this->set('type', $type);
    }

    public function storage_location_added()
    {
        $this->set('message', t('File storage location added.'));
        $this->view();
    }

    public function storage_location_deleted()
    {
        $this->set('message', t('File storage location removed.'));
        $this->view();
    }

    public function storage_location_updated()
    {
        $this->set('message', t('File storage location saved.'));
        $this->view();
    }

    public function edit($fslID = false)
    {
        $location = FileStorageLocation::getByID($fslID);
        if (is_object($location)) {
            $this->set('location', $location);
            $this->set('type', $location->getTypeObject());
        } else {
            $this->redirect('/dashboard/system/files/storage');
        }
    }

    protected function validateStorageRequest()
    {
        $request = \Request::getInstance();
        $val = Loader::helper('validation/strings');
        $type = Type::getByID($request->get('fslTypeID'));

        if (!is_object($type)) {
            $this->error->add(t('Invalid type object.'));
        } else {
            $e = $type->getConfigurationObject()->validateRequest($request);
            if (is_object($e)) {
                $this->error->add($e);
            }
        }

        if (!$val->notempty($request->request->get('fslName'))) {
            $this->error->add(t('Your file storage location must have a name.'));
        }

        return array($request, $type);
    }

    public function update()
    {
        list($request, $type) = $this->validateStorageRequest();

        $fsl = FileStorageLocation::getByID($request->request->get('fslID'));
        if (!Loader::helper('validation/token')->validate('update')) {
            $this->error->add(Loader::helper('validation/token')->getErrorMessage());
        }
        if (!is_object($fsl)) {
            $this->error->add(t('Invalid file storage location object.'));
        }
        if (!$this->error->has()) {
            $configuration = $type->getConfigurationObject();
            $configuration->loadFromRequest($request);
            $fsl->setName($request->request->get('fslName'));
            if (!$fsl->isDefault()) {
                $fsl->setIsDefault($request->request->get('fslIsDefault'));
            }
            $fsl->setConfigurationObject($configuration);
            $fsl->save();
            $this->redirect('/dashboard/system/files/storage', 'storage_location_updated');
        }

        $this->edit($request->request->get('fslID'));
    }

    public function delete()
    {
        $request = \Request::getInstance();

        if (!Loader::helper('validation/token')->validate('delete')) {
            $this->error->add(Loader::helper('validation/token')->getErrorMessage());
        }
        $fsl = FileStorageLocation::getByID($request->request->get('fslID'));
        if (!is_object($fsl)) {
            $this->error->add(t('Invalid file storage location object.'));
        }
        if ($fsl->isDefault()) {
            $this->error->add(t('You may not delete the default file storage location.'));
        }

        if (!$this->error->has()) {
            $fsl->delete();
            $this->redirect('/dashboard/system/files/storage', 'storage_location_deleted');
        }
        $this->edit($request->request->get('fslID'));
    }

    public function add()
    {
        list($request, $type) = $this->validateStorageRequest();
        if (!Loader::helper('validation/token')->validate('add')) {
            $this->error->add(Loader::helper('validation/token')->getErrorMessage());
        }
        if (!$this->error->has()) {
            $configuration = $type->getConfigurationObject();
            $configuration->loadFromRequest($request);
            $fsl = FileStorageLocation::add($configuration,
                $request->request->get('fslName'),
                $request->request->get('fslIsDefault')
            );

            $this->redirect('/dashboard/system/files/storage', 'storage_location_added');
        }

        $this->set('type', $type);
    }
}
