<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Files;

use Concrete\Core\Page\Controller\DashboardPageController;
use Config;

class ImageUploading extends DashboardPageController
{
    public $helpers = array('form', 'concrete/ui', 'validation/token', 'concrete/file');

    public function view()
    {
        $this->set('restrict_uploaded_image_sizes', Config::get('concrete.file_manager.restrict_uploaded_image_sizes'));
        $this->set('restrict_max_width', Config::get('concrete.file_manager.restrict_max_width'));
        $this->set('restrict_max_height', Config::get('concrete.file_manager.restrict_max_height'));
        $this->set('restrict_resize_quality', Config::get('concrete.file_manager.restrict_resize_quality'));
    }

    public function saved()
    {
        $this->set('message', t('Image uploading settings saved.'));
        $this->view();
    }

    public function save()
    {
        $quality = (int) $this->post('restrict_resize_quality');

        if ($quality < 1 or $quality > 100) {
            $quality = 85;
        }

        $maxwidth = (int) $this->post('restrict_max_width');

        if (!$maxwidth || $maxwidth < 1) {
            $maxwidth = 1920;
        }

        $maxheight = (int) $this->post('restrict_max_height');

        if (!$maxheight || $maxheight < 1) {
            $maxheight = 1080;
        }

        Config::save('concrete.file_manager.restrict_uploaded_image_sizes', (bool) $this->post('restrict_uploaded_image_sizes'));
        Config::save('concrete.file_manager.restrict_max_width', $maxwidth);
        Config::save('concrete.file_manager.restrict_max_height', $maxheight);
        Config::save('concrete.file_manager.restrict_resize_quality', $quality);
        $this->redirect('/dashboard/system/files/image_uploading', 'saved');
    }
}
