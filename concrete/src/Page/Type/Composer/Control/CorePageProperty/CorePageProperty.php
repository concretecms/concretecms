<?php
namespace Concrete\Core\Page\Type\Composer\Control\CorePageProperty;

use Loader;
use Environment;
use Concrete\Core\Page\Page;
use Controller;

abstract class CorePageProperty extends \Concrete\Core\Page\Type\Composer\Control\Control
{
    protected $propertyHandle;
    protected $ptComposerControlTypeHandle = 'core_page_property';
    private static $ptComposerSaveRequest = null;
    private static $ptComposerRequestControlsProcessed = array();

    public function addAssetsToRequest(Controller $cnt)
    {
    }

    public static function clearComposerRequestProcessControls()
    {
        self::$ptComposerRequestControlsProcessed = array();
    }

    public function removePageTypeComposerControlFromPage()
    {
        return false;
    }

    public function isPageTypeComposerControlValueEmpty()
    {
        return $this->getPageTypeComposerControlDraftValue() != '';
    }

    public function export($node)
    {
        $node->addAttribute('handle', $this->getCorePagePropertyHandle());
    }

    public static function getPageTypeComposerSaveRequest()
    {
        if (null === self::$ptComposerSaveRequest) {
            self::$ptComposerSaveRequest = array();
        }

        return self::$ptComposerSaveRequest;
    }

    public function shouldPageTypeComposerControlStripEmptyValuesFromPage()
    {
        return false;
    }

    public function pageTypeComposerFormControlSupportsValidation()
    {
        return true;
    }

    public function addPageTypeComposerControlRequestValue($key, $value)
    {
        self::$ptComposerSaveRequest[$key] = $value;
    }

    public function setCorePagePropertyHandle($propertyHandle)
    {
        $this->setPageTypeComposerControlIdentifier($propertyHandle);
        $this->propertyHandle = $propertyHandle;
    }

    public function getCorePagePropertyHandle()
    {
        return $this->propertyHandle;
    }

    public function getPageTypeComposerControlCustomTemplates()
    {
        return array();
    }

    public function publishToPage(Page $c, $data, $controls)
    {
        array_push(self::$ptComposerRequestControlsProcessed, $this);
        // now we check to see if we have any more core controls to process in this request
        $coreControls = array();
        foreach ($controls as $cnt) {
            if ($cnt->getPageTypeComposerControlTypeHandle() == $this->ptComposerControlTypeHandle) {
                $coreControls[] = $controls;
            }
        }
        if (count(self::$ptComposerRequestControlsProcessed) == count($coreControls)) {
            // this was the last one. so we're going to loop through our saved request
            // and do the page update once, rather than four times.
            $c->update(self::$ptComposerSaveRequest);
        }
    }

    public function render($label, $customTemplate, $description)
    {
        $env = Environment::get();
        $form = Loader::helper('form');
        $set = $this->getPageTypeComposerFormLayoutSetControlObject()->getPageTypeComposerFormLayoutSetObject();
        $control = $this;

        if ($customTemplate) {
            $rec = $env->getRecord(DIRNAME_ELEMENTS . '/' . DIRNAME_PAGE_TYPES . '/' . DIRNAME_COMPOSER . '/' . DIRNAME_COMPOSER_ELEMENTS_CONTROLS . '/' . $this->ptComposerControlTypeHandle . '/' . $this->propertyHandle . '/' . DIRNAME_BLOCK_TEMPLATES_COMPOSER . '/' . $customTemplate);
            if ($rec->exists()) {
                $template = $rec->file;
            }
        }

        if (!isset($template)) {
            $template = $env->getPath(DIRNAME_ELEMENTS . '/' . DIRNAME_PAGE_TYPES . '/' . DIRNAME_COMPOSER . '/' . DIRNAME_COMPOSER_ELEMENTS_CONTROLS . '/' . $this->ptComposerControlTypeHandle . '/' . $this->propertyHandle . '.php');
        }

        include $template;
    }

    public function validate()
    {
        return true;
    }
}
