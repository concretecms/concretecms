<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Files;

use Concrete\Core\Page\Controller\DashboardPageController;
use Config;
use Loader;

class Filetypes extends DashboardPageController
{
    public $helpers = array('form', 'concrete/ui', 'concrete/file');

    public function view()
    {
        $config = $this->app->make('config');
        $helper_file = $this->app->make('helper/concrete/file');
        
        $file_access_file_types = $helper_file->unserializeUploadFileExtensions($config->get('concrete.upload.extensions'));
        $file_access_file_types = implode(', ', $file_access_file_types);
        $this->set('file_access_file_types', $file_access_file_types);

        $file_access_file_types_denylist = $helper_file->unserializeUploadFileExtensions($config->get('concrete.upload.extensions_denylist', $config->get('concrete.upload.extensions_blacklist')));
        $this->set('file_access_file_types_denylist', $file_access_file_types_denylist);
    }

    public function saved()
    {
        $this->set('message', t('Allowed file types saved.'));
        $this->view();
    }

    public function file_access_extensions()
    {
        $helper_file = Loader::helper('concrete/file');
        $validation_token = Loader::helper('validation/token');

        if (!$validation_token->validate("file_access_extensions")) {
            $this->set('error', array($validation_token->getErrorMessage()));

            return;
        }

        $types = preg_split('{,}', $this->post('file-access-file-types'), null, PREG_SPLIT_NO_EMPTY);
        $types = $helper_file->serializeUploadFileExtensions($types);
        Config::save('concrete.upload.extensions', $types);
        $this->redirect('/dashboard/system/files/filetypes', 'saved');
    }
}
