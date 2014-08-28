<?php
namespace Concrete\Block\ExternalForm;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Foundation\Object;
use Loader;

class Controller extends BlockController
{

    public $helpers = array('file');
    protected $btTable = 'btExternalForm';
    protected $btInterfaceWidth = "370";
    protected $btInterfaceHeight = "175";
    protected $btCacheBlockRecord = true;
    protected $btWrapperClass = 'ccm-ui';

    /**
     * Used for localization. If we want to localize the name/description we have to include this
     */
    public function getBlockTypeDescription()
    {
        return t("Include external forms in the filesystem and place them on pages.");
    }

    public function getBlockTypeName()
    {
        return t("External Form");
    }

    public function getJavaScriptStrings()
    {
        return array('form-required' => t('You must select a form.'));
    }

    function getFilename()
    {
        return $this->filename;
    }

    function getExternalFormFilenamePath()
    {
        if (file_exists(DIR_FILES_BLOCK_TYPES_FORMS_EXTERNAL . '/' . $this->filename)) {
            $filename = DIR_FILES_BLOCK_TYPES_FORMS_EXTERNAL . '/' . $this->filename;
        } else {
            if (file_exists(DIR_FILES_BLOCK_TYPES_FORMS_EXTERNAL_CORE . '/' . $this->filename)) {
                $filename = DIR_FILES_BLOCK_TYPES_FORMS_EXTERNAL_CORE . '/' . $this->filename;
            }
        }
        return $filename;
    }

    public function isValidControllerTask($method, $parameters = array())
    {
        $class = camelcase(substr($this->filename, 0, strrpos($this->filename, '.php')));
        $controller = \Core::make('\\Concrete\\Block\\ExternalForm\\Form\\Controller\\' . $class);
        if (method_exists($controller, $method)) {
            return true;
        }
        return parent::isValidControllerTask($method, $parameters);

    }

    protected function getController()
    {
        $class = camelcase(substr($this->filename, 0, strrpos($this->filename, '.php')));
        return \Core::make('\\Concrete\\Block\\ExternalForm\\Form\\Controller\\' . $class);
    }

    public function runAction($method, $parameters)
    {
        if (in_array($method, array('add', 'edit'))) {
            parent::runAction($method, $parameters);
            return;
        }

        $controller = $this->getController();
        $controller->runAction($method, $parameters);
        foreach($controller->getSets() as $key => $value) {
            $this->set($key, $value);
        }
    }

    public function add()
    {
        $this->set('filenames', $this->getFormList());
    }

    public function getFormList()
    {

        $forms = array();
        $fh = Loader::helper('file');

        if (file_exists(DIR_FILES_BLOCK_TYPES_FORMS_EXTERNAL)) {
            $forms = array_merge(
                $forms,
                $fh->getDirectoryContents(DIR_FILES_BLOCK_TYPES_FORMS_EXTERNAL, array('controller')));
        }
        if (file_exists(DIR_FILES_BLOCK_TYPES_FORMS_EXTERNAL_CORE)) {
            $forms = array_merge(
                $forms,
                $fh->getDirectoryContents(DIR_FILES_BLOCK_TYPES_FORMS_EXTERNAL_CORE, array('controller')));
        }

        return $forms;
    }

    public function edit()
    {
        $this->set('filenames', $this->getFormList());
    }

}
