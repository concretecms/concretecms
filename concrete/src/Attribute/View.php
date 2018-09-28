<?php
namespace Concrete\Core\Attribute;

use Concrete\Core\Attribute\Context\AttributeTypeSettingsContext;
use Concrete\Core\Attribute\Context\BasicFormContext;
use Concrete\Core\Attribute\Context\BasicSearchContext;
use Concrete\Core\Attribute\Context\ComposerContext;
use Concrete\Core\Filesystem\TemplateLocation;
use Concrete\Core\Form\Context\ContextInterface;
use Concrete\Core\Filesystem\TemplateLocator;
use Loader;
use Concrete\Core\Attribute\Value\Value as AttributeValue;
use Concrete\Core\Attribute\Key\Key as AttributeKey;
use Environment;
use Concrete\Core\View\AbstractView;

class View extends AbstractView
{
    protected $attributeValue;
    protected $attributeKey;
    protected $attributeType;
    protected $attributePkgHandle;
    protected $templateLocator;
    protected $controllerAction;

    protected function getValue()
    {
        return $this->attributeValue;
    }

    protected function getAttributeKey()
    {
        return $this->attributeKey;
    }

    protected function constructView($mixed)
    {
        if ($mixed instanceof AttributeValueInterface) {
            $this->attributeValue = $mixed;
            $this->attributeKey = $mixed->getAttributeKey();
            $this->attributeType = $mixed->getAttributeTypeObject();
        } else {
            if ($mixed instanceof AttributeKeyInterface) {
                $this->attributeKey = $mixed;
                $this->attributeType = $this->attributeKey->getAttributeType();
            } else {
                $this->attributeType = $mixed;
            }
        }
        $this->attributePkgHandle = $this->attributeType->getPackageHandle();
    }

    /**
     * @deprecated
     */
    protected function getContextFromString($string)
    {
        switch($string) {
            case 'form':
                $context = new BasicFormContext();
                break;
            case 'composer':
                $context = new ComposerContext();
                break;
            case 'search':
                $context = new BasicSearchContext();
                break;
            case 'type_form':
                $context = new AttributeTypeSettingsContext();
                break;
            case 'label':
                $context = 'label';
                break;
        }
        return $context;
    }

    /**
     * @param $start ContextInterface
     */
    public function start($context)
    {
        if (is_string($context)) {
            $context = $this->getContextFromString($context);
        }

        $atHandle = $this->attributeType->getAttributeTypeHandle();

        $this->setupController();

        if (is_object($context)) {
            $this->templateLocator = new TemplateLocator();
            foreach($context->getControlTemplates() as $template) {
                $pkgHandle = $template[1] ? $template[1] : $this->attributePkgHandle;
                $location = new TemplateLocation(DIRNAME_ATTRIBUTES . '/' . $atHandle . '/' . $template[0] . '.php', $pkgHandle);
                $this->templateLocator->addLocation($location);
            }
            foreach($context->getActions() as $method) {
                if (method_exists($this->controller, $method)) {
                    $this->controllerAction = $method;
                    break;
                }
            }
        } else if ($context == 'label') {
            // sigh. legacy
            $this->controllerAction = 'label';
        }


    }

    public function startRender()
    {
        if (isset($this->templateLocator)) {
            $location = $this->templateLocator->getLocation();
            $file = basename($location->getFile());
            // turn /path/to/my/file/view.php into view
            $name = substr($file, 0, strpos($file, '.php'));
            $js = $this->controller->getAttributeTypeFileURL($name . '.js');
            $css = $this->controller->getAttributeTypeFileURL($name . '.css');
            $html = Loader::helper('html');
            if ($js != false) {
                $this->addOutputAsset($html->javascript($js));
            }
            if ($css != false) {
                $this->addOutputAsset($html->css($css));
            }
        }
    }

    public function setupRender()
    {
        $this->runControllerTask();
        if (isset($this->templateLocator)) {
            $file = $this->templateLocator->getFile();
            $this->setViewTemplate($file);
        }
    }

    public function setupController()
    {
        if (!$this->controller) {
            $this->controller = $this->attributeType->getController();
            $this->controller->setAttributeKey($this->attributeKey);
            $this->controller->setAttributeValue($this->attributeValue);
            if (is_object($this->attributeKey)) {
                $this->controller->set('akID', $this->attributeKey->getAttributeKeyID());
            }
        }
    }

    public function runControllerTask()
    {
        $this->controller->on_start();
        $this->controller->runAction($this->controllerAction);
        $this->controller->on_before_render();
    }

    public function action($action)
    {
        $arguments = array();
        if (count(func_get_args()) > 1) {
            $arguments = array_unshift(func_get_args());
        }
        if (is_object($this->attributeKey)) {
            return (string)
            \URL::to('/ccm/system/attribute/action/key', $this->attributeKey->getAttributeKeyID(), $action,
                $arguments);
        } else {
            return (string)
            \URL::to('/ccm/system/attribute/action/type', $this->controller->attributeType->getAttributeTypeID(),
                $action, $arguments);
        }
    }

    public function finishRender($contents)
    {
        echo $contents;
    }

    protected function onBeforeGetContents()
    {
    }

    protected function onAfterGetContents()
    {
    }

    public function field($fieldName)
    {
        return $this->controller->field($fieldName);
    }
}
