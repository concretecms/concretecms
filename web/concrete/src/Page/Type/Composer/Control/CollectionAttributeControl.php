<?php
namespace Concrete\Core\Page\Type\Composer\Control;

use Loader;
use \Concrete\Core\Foundation\Object;
use Controller;
use CollectionAttributeKey;
use Page;
use Environment;

class CollectionAttributeControl extends Control
{
    protected $akID;
    protected $ak = false;
    protected $ptComposerControlTypeHandle = 'collection_attribute';

    public function setAttributeKeyID($akID)
    {
        $this->akID = $akID;
        $this->setPageTypeComposerControlIdentifier($akID);
    }

    public function pageTypeComposerFormControlSupportsValidation()
    {
        return true;
    }

    public function export($node)
    {
        $ak = $this->getAttributeKeyObject();
        $node->addAttribute('handle', $ak->getAttributeKeyHandle());
    }

    public function removePageTypeComposerControlFromPage()
    {
        $ak = $this->getAttributeKeyObject();
        if (is_object($ak)) {
            $this->page->clearAttribute($ak);
        }
    }

    public function getAttributeKeyObject()
    {
        if (!$this->ak) {
            $this->ak = CollectionAttributeKey::getByID($this->akID);
        }

        return $this->ak;
    }

    public function getAttributeKeyID()
    {
        return $this->akID;
    }

    public function getPageTypeComposerControlCustomTemplates()
    {
        return array();
    }

    public function canPageTypeComposerControlSetPageName()
    {
        $ak = $this->getAttributeKeyObject();
        if (is_object($ak) && $ak->getAttributeKeyHandle() == 'meta_title') {
            return true;
        }

        return false;
    }

    public function getPageTypeComposerControlPageNameValue(Page $c)
    {
        $ak = $this->getAttributeKeyObject();
        if (is_object($ak)) {
            return $c->getAttribute($ak->getAttributeKeyHandle());
        }
    }

    public function getPageTypeComposerControlDraftValue()
    {
        if (is_object($this->page)) {
            $ak = $this->getAttributeKeyObject();
            $c = $this->page;
            if (is_object($ak)) {
                return $c->getAttributeValueObject($ak);
            }
        }
    }

    public function shouldPageTypeComposerControlStripEmptyValuesFromPage()
    {
        return true;
    }

    public function isPageTypeComposerControlValueEmpty()
    {
        $ak = $this->getAttributeKeyObject();
        $c = $this->page;
        if (!is_object($ak)) {
            return true;
        }

        return ($c->getAttribute($ak) == '');
    }

    public function render($label, $customTemplate, $description)
    {
        $ak = $this->getAttributeKeyObject();
        if (is_object($ak)) {
            $env = Environment::get();
            $set = $this->getPageTypeComposerFormLayoutSetControlObject()->getPageTypeComposerFormLayoutSetObject();
            $control = $this;
            $template = $env->getPath(
                DIRNAME_ELEMENTS . '/' . DIRNAME_PAGE_TYPES . '/' . DIRNAME_COMPOSER . '/' . DIRNAME_COMPOSER_ELEMENTS_CONTROLS . '/' . $this->ptComposerControlTypeHandle . '.php'
            );
            include $template;
        }
    }

    public function publishToPage(Page $c, $data, $controls)
    {
        // the data for this actually doesn't come from $data. Attributes have their own way of gettin data.
        $ak = $this->getAttributeKeyObject();
        if (is_object($ak)) {
            $ak->saveAttributeForm($c);
        }
    }

    protected function isFormSubmission()
    {
        $ak = $this->getAttributeKeyObject();
        $controller = $ak->getController();
        if (is_object($controller)) {
            return $controller->requestFieldExists();
        }
    }

    public function validate()
    {
        $ak = $this->getAttributeKeyObject();
        if (is_object($ak)) {

            $e = \Core::make('error');
            if ($this->isFormSubmission()) {
                $response = $ak->validateAttributeForm();
                if ($response == false) {
                    $e->add(t('The field "%s" is required', $ak->getAttributeKeyDisplayName()));
                } else if ($response instanceof \Concrete\Core\Error\Error) {
                    $e->add($response);
                }
            } else {
                $value = $this->getPageTypeComposerControlDraftValue();
                if (!is_object($value)) {
                    $e->add(t('The field "%s" is required', $ak->getAttributeKeyDisplayName()));
                } else {
                    $response = $value->validateAttributeValue();
                    if ($response == false) {
                        $e->add(t('The field "%s" is required', $ak->getAttributeKeyDisplayName()));
                    } else if ($response instanceof \Concrete\Core\Error\Error) {
                        $e->add($response);
                    }
                }
            }

            return $e;
        }
    }

    public function addAssetsToRequest(Controller $cnt)
    {
        $ak = $this->getAttributeKeyObject();
        $akc = $ak->getController();
        $akc->setupAndRun('composer');
    }

}
