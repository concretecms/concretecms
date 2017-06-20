<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Files;

use Concrete\Core\File\Image\Thumbnail\Type\Type;
use Concrete\Core\Page\Controller\DashboardPageController;
use Loader;
use Request;

class Thumbnails extends DashboardPageController
{
    public function view()
    {
        $list = Type::getList();
        $this->set('types', $list);
    }

    public function edit($ftTypeID = false)
    {
        $type = Type::getByID($ftTypeID);
        $this->set('type', $type);

        $this->get_sizing_values();
    }

    public function add()
    {
        $this->get_sizing_values();
    }

    protected function get_sizing_values()
    {
        $sizingModes = [
            Type::RESIZE_PROPORTIONAL => t('Resize Proportionally'),
            Type::RESIZE_EXACT => t('Resize and Crop to the Exact Size')
        ];
        $this->set('sizingModes', $sizingModes);

        $sizingModeHelp = [
            Type::RESIZE_PROPORTIONAL => t("The original image will be scaled down so it is fully contained within the thumbnail dimensions. The specified width and height will be considered maximum limits. Unless the given dimensions are equal to the original image's aspect ratio, one dimension in the resulting thumbnail will be smaller than the given limit."),
            Type::RESIZE_EXACT => t("The thumbnail will be scaled so that its smallest side will equal the length of the corresponding side in the original image. Any excess outside of the scaled thumbnail's area will be cropped, and the returned thumbnail will have the exact width and height specified. Both width and height must be specified.")
        ];
        $this->set('sizingModeHelp', $sizingModeHelp);

        $this->set('ftTypeSizingMode', Type::RESIZE_DEFAULT);

        $this->set('sizingHelpText', $sizingModeHelp[Type::RESIZE_DEFAULT]);
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
        $valStrings = Loader::helper('validation/strings');
        $valNumbers = Loader::helper('validation/numbers');

        if (!$valStrings->notempty($request->request->get('ftTypeName'))) {
            $this->error->add(t("Your thumbnail type must have a name."));
        }

        if (!$valStrings->handle($request->request->get('ftTypeHandle'))) {
            $this->error->add(t("Your thumbnail type handle must only contain lowercase letters and underscores."));
        }

        $width = (int) $request->request->get('ftTypeWidth');
        $height = (int) $request->request->get('ftTypeHeight');
        if ($width < 1 && $height < 1) {
            $this->error->add(t("Width and height can't both be empty or less than zero."));
        }

        if ($request->request->get('ftTypeSizingMode') === Type::RESIZE_EXACT && ($width < 1 || $height < 1)) {
            $this->error->add(t("With the 'Exact' sizing mode (with cropping), both width and height must be specified and greater than zero."));
        }

        if ($valStrings->notempty($width)) {
            if (!$valNumbers->integer($width)) {
                $this->error->add(t("If used, width can only be an integer, with no units."));
            } else {
                if ($width < 1) {
                    $this->error->add(t("If used, width must be greater than zero."));
                }
            }
        }

        if ($valStrings->notempty($height)) {
            if (!$valNumbers->integer($height)) {
                $this->error->add(t("If used, height can only be an integer, with no units."));
            } else {
                if ($height < 1) {
                    $this->error->add(t("If used, height must be greater than zero."));
                }
            }
        }

        return $request;
    }

    public function thumbnail_type_deleted()
    {
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
            $height = (int) $request->request->get('ftTypeHeight');
            $width = (int) $request->request->get('ftTypeWidth');
            if ($height > 0) {
                $type->setHeight($height);
            } else {
                $type->setHeight(null);
            }
            if ($width > 0) {
                $type->setWidth($width);
            } else {
                $type->setWidth(null);
            }
            $type->setName($request->request->get('ftTypeName'));
            $type->setHandle($request->request->get('ftTypeHandle'));
            $type->setSizingMode($request->request->get('ftTypeSizingMode'));
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
        $thumbtype = Type::getByHandle($request->request->get('ftTypeHandle'));
        if (is_object($thumbtype)) {
            $this->error->add(t('That handle is in use.'));
        }
        if (!$this->error->has()) {
            $type = new \Concrete\Core\Entity\File\Image\Thumbnail\Type\Type();
            $height = (int) $request->request->get('ftTypeHeight');
            $width = (int) $request->request->get('ftTypeWidth');
            if ($height > 0) {
                $type->setHeight($height);
            }
            if ($width > 0) {
                $type->setWidth($width);
            }
            
            $type->setName($request->request->get('ftTypeName'));
            $type->setHandle($request->request->get('ftTypeHandle'));
            $type->setSizingMode($request->request->get('ftTypeSizingMode'));
            $type->save();
            $this->redirect('/dashboard/system/files/thumbnails', 'thumbnail_type_added');
        }

        $this->set('type', $type);
        $this->get_sizing_values();
    }
}
