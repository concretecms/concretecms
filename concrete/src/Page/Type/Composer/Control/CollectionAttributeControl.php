<?php
namespace Concrete\Core\Page\Type\Composer\Control;

use Concrete\Core\Attribute\Context\ComposerContext;
use Concrete\Core\Validation\ResponseInterface;
use Controller;
use CollectionAttributeKey;
use Concrete\Core\Page\Page;
use Environment;

class CollectionAttributeControl extends Control
{
    protected $akID;
    protected $ak = false;
    protected $ptComposerControlTypeHandle = 'collection_attribute';

    public function __sleep()
    {
        return array('akID');
    }

    public function setAttributeKeyID($akID)
    {
        $this->akID = $akID;
        $this->setPageTypeComposerControlIdentifier($akID);
    }

    public function getPageTypeComposerControlName()
    {
        return $this->getAttributeKeyObject()->getAttributeKeyDisplayName('text');
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

    /**
     * @return CollectionAttributeKey
     */
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
        } else {
            $control = $this->getPageTypeComposerFormLayoutSetControlObject();
            if (is_object($control)) {
                $set = $control->getPageTypeComposerFormLayoutSetObject();
                if (is_object($set)) {
                    $type = $set->getPageTypeObject();
                    // Does this page type have default attributes for this
                    // attribute ?
                    $defaults = $type->getPageTypePageTemplateDefaultPageObject();
                    if (is_object($defaults)) {
                        $value = $defaults->getAttributeValue($this->getAttributeKeyObject());
                        if (is_object($value)) {
                            return $value;
                        }
                    }
                }
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

        return $c->getAttribute($ak) == '';
    }

    public function render($label, $customTemplate, $description)
    {
        $ak = $this->getAttributeKeyObject();
        if (is_object($ak)) {
            $env = Environment::get();
            $set = $this->getPageTypeComposerFormLayoutSetControlObject()->getPageTypeComposerFormLayoutSetObject();
            $control = $this;

            $context = new ComposerContext();
            $context->setTooltip($description);
            if ($this->isPageTypeComposerControlRequiredByDefault() || $this->isPageTypeComposerFormControlRequiredOnThisRequest()) {
                $context->setRequired(true);
            }
            $view = $ak->getControlView($context);
            $view->setValue($this->getPageTypeComposerControlDraftValue());
            $view->setLabel($label);
            $renderer = $view->getControlRenderer();
            print $renderer->render();
        }
    }

    public function publishToPage(Page $c, $data, $controls)
    {
        // the data for this actually doesn't come from $data. Attributes have their own way of gettin data.
        $ak = $this->getAttributeKeyObject();
        if (is_object($ak)) {
            $controller = $ak->getController();
            $value = $controller->createAttributeValueFromRequest();
            $c->setAttribute($ak, $value);
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
                $controller = $ak->getController();
                $validator = $controller->getValidator();
                $control = $this->getPageTypeComposerFormLayoutSetControlObject();
                $response = $validator->validateSaveValueRequest(
                    $controller, \Request::createFromGlobals()
                );
                /**
                 * @var $response ResponseInterface
                 */
                if (!$response->isValid()) {
                    $error = $response->getErrorObject();
                    $e->add($error);
                }
            } else {
                $value = $this->getPageTypeComposerControlDraftValue();
                if (!is_object($value)) {
                    $control = $this->getPageTypeComposerFormLayoutSetControlObject();
                    $e->add(t('The field %s is required', $control->getPageTypeComposerControlLabel()));
                } else {
                    $controller = $ak->getController();
                    $validator = $controller->getValidator();
                    $response = $validator->validateCurrentAttributeValue($controller, $value);
                    if (!$response->isValid()) {
                        $error = $response->getErrorObject();
                        $e->add($error);
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

    public function objectExists()
    {
        return $this->getAttributeKeyObject() !== null;
    }
}
