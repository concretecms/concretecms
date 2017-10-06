<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Files;

use Concrete\Core\File\StorageLocation\StorageLocationFactory;
use Concrete\Core\File\StorageLocation\Type\Type;
use Concrete\Core\Page\Controller\DashboardPageController;

class Storage extends DashboardPageController
{
    public function view()
    {
        $this->set('locations', $this->app->make(StorageLocationFactory::class)->fetchList());
        $types = [];
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
        $location = $fslID ? $this->app->make(StorageLocationFactory::class)->fetchByID($fslID) : null;
        if ($location !== null) {
            /* @var \Concrete\Core\Entity\File\StorageLocation\StorageLocation $location */
            $this->set('location', $location);
            $this->set('type', $location->getTypeObject());
        } else {
            $this->redirect('/dashboard/system/files/storage');
        }
    }

    /**
     * @return \Concrete\Core\Entity\File\StorageLocation\Type\Type|null
     */
    protected function validateStorageRequest()
    {
        $val = $this->app->make('helper/validation/strings');
        $type = Type::getByID($this->request->get('fslTypeID'));
        if ($type === null) {
            $this->error->add(t('Invalid type object.'));
        } else {
            $e = $type->getConfigurationObject()->validateRequest($this->request);
            if (is_object($e)) {
                $this->error->add($e);
            }
        }
        if (!$val->notempty($this->request->request->get('fslName'))) {
            $this->error->add(t('Your file storage location must have a name.'));
        }

        return $type;
    }

    public function update()
    {
        $type = $this->validateStorageRequest();
        $post = $this->request->request;

        $fslID = $post->get('fslID');
        $fsl = $fslID ? $this->app->make(StorageLocationFactory::class)->fetchByID($fslID) : null;
        /* @var \Concrete\Core\Entity\File\StorageLocation\StorageLocation|null $fsl */
        if (!$this->token->validate('update')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if ($fsl === null) {
            $this->error->add(t('Invalid file storage location object.'));
        }
        if (!$this->error->has()) {
            $configuration = $type->getConfigurationObject();
            $configuration->loadFromRequest($this->request);
            $fsl->setName($post->get('fslName'));
            if (!$fsl->isDefault()) {
                $fsl->setIsDefault($post->get('fslIsDefault'));
            }
            $fsl->setConfigurationObject($configuration);
            $fsl->save();
            $this->redirect('/dashboard/system/files/storage', 'storage_location_updated');
        }
        $this->edit($fslID);
    }

    public function delete()
    {
        if (!$this->token->validate('delete')) {
            $this->error->add($this->token->getErrorMessage());
        }
        $fslID = $this->request->request->get('fslID');
        $fsl = $fslID ? $this->app->make(StorageLocationFactory::class)->fetchByID($fslID) : null;
        /* @var \Concrete\Core\Entity\File\StorageLocation\StorageLocation|null $fsl */
        if ($fsl === null) {
            $this->error->add(t('Invalid file storage location object.'));
        } elseif ($fsl->isDefault()) {
            $this->error->add(t('You may not delete the default file storage location.'));
        }
        if (!$this->error->has()) {
            $fsl->delete();
            $this->redirect('/dashboard/system/files/storage', 'storage_location_deleted');
        }
        $this->edit($fslID);
    }

    public function add()
    {
        $type = $this->validateStorageRequest();
        if (!$this->token->validate('add')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$this->error->has()) {
            $configuration = $type->getConfigurationObject();
            $configuration->loadFromRequest($this->request);
            $factory = $this->app->make(StorageLocationFactory::class);
            /* @var StorageLocationFactory $factory */
            $location = $factory->create($configuration, $this->request->request->get('fslName'));
            $location->setIsDefault($this->request->request->get('fslIsDefault'));
            $location = $factory->persist($location);
            $this->redirect('/dashboard/system/files/storage', 'storage_location_added');
        }
        $this->set('type', $type);
    }
}
