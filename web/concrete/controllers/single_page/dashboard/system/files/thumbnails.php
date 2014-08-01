<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Files;
use Concrete\Core\File\Image\Thumbnail\Type\Type;
use \Concrete\Core\Page\Controller\DashboardPageController;
use Loader;
use Request;

class Thumbnails extends DashboardPageController {

	public function view() {
        $list = Type::getList();
        $this->set('types', $list);
	}

    public function edit($ftTypeID = false)
    {
        $type = Type::getByID($ftTypeID);
        $this->set('type', $type);
    }

    public function add()
    {

    }

    public function thumbnail_type_added()
    {
        $this->set('success', t('Thumbnail type added.'));
        $this->view();
    }

    public function thumbnail_type_updated()
    {
        $this->set('success', t('Thumbnail type updated.'));
        $this->view();
    }

    protected function validateThumbnailRequest()
    {
        $request = \Request::getInstance();
        $val = Loader::helper('validation/strings');

        if (!$val->notempty($request->request->get('ftTypeName'))) {
            $this->error->add(t('Your thumbnail type must have a name.'));
        }

        if (!$val->handle($request->request->get('ftTypeHandle'))) {
            $this->error->add(t('Your thumbnail type handle must only contain lowercase letters and underscores.'));
        }

        $width = intval($request->request->get('ftTypeWidth'));
        if ($width < 1) {
            $this->error->add(t('Width must be greater than zero.'));
        }

        return $request;
    }

    public function thumbnail_type_deleted() {
        $this->set('message', t('Thumbnail type removed.'));
        $this->view();
    }

    public function delete()
    {
        $request = \Request::getInstance();

        if (!Loader::helper('validation/token')->validate('delete')) {
            $this->error->add(Loader::helper('validation/token')->getErrorMessage());
        }
        $type = Type::getByID($request->request->get('ftTypeID'));
        if (!is_object($type)) {
            $this->error->add(t('Invalid thumbnail type object.'));
        }
        if ($type->isRequired()) {
            $this->error->add(t('You may not delete a required thumbnail type.'));
        }

        if (!$this->error->has()) {
            $type->delete();
            $this->redirect('/dashboard/system/files/thumbnails', 'thumbnail_type_deleted');
        }
        $this->edit($request->request->get('ftTypeID'));

    }

    public function update()
    {
        $request = $this->validateThumbnailRequest();

        $type = Type::getByID($request->request->get('ftTypeID'));
        if (!Loader::helper('validation/token')->validate('update')) {
            $this->error->add(Loader::helper('validation/token')->getErrorMessage());
        }
        if (!is_object($type)) {
            $this->error->add(t('Invalid thumbnail type object.'));
        }
        if (!$this->error->has()) {
            $type->setWidth($request->request->get('ftTypeWidth'));
            $height = intval($request->request->get('ftTypeHeight'));
            if ($height > 0) {
                $type->setHeight($request->request->get('ftTypeHeight'));
            } else {
                $type->setHeight(null);
            }
            $type->setName($request->request->get('ftTypeName'));
            $type->setHandle($request->request->get('ftTypeHandle'));
            $type->save();
            $this->redirect('/dashboard/system/files/thumbnails', 'thumbnail_type_updated');
        }

        $this->edit($request->request->get('ftTypeID'));
    }

    public function do_add()
    {
        $request = $this->validateThumbnailRequest();
        if (!Loader::helper('validation/token')->validate('do_add')) {
            $this->error->add(Loader::helper('validation/token')->getErrorMessage());
        }
        if (!$this->error->has()) {
            $type = new Type();
            $height = intval($request->request->get('ftTypeHeight'));
            if ($height > 0) {
                $type->setHeight($request->request->get('ftTypeHeight'));
            }
            $type->setWidth($request->request->get('ftTypeWidth'));
            $type->setName($request->request->get('ftTypeName'));
            $type->setHandle($request->request->get('ftTypeHandle'));
            $type->save();
            $this->redirect('/dashboard/system/files/thumbnails', 'thumbnail_type_added');
        }

        $this->set('type', $type);
    }
}