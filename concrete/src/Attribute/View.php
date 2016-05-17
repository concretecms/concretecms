<?php
namespace Concrete\Core\Attribute;

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
    protected $initialViewToRender;
    protected $viewToRender;

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
        $this->controller = $this->attributeType->getController();
        $this->controller->setAttributeKey($this->attributeKey);
        $this->controller->setAttributeValue($this->attributeValue);
        if (is_object($this->attributeKey)) {
            $this->controller->set('akID', $this->attributeKey->getAttributeKeyID());
        }
    }

    public function start($state)
    {
        $this->initialViewToRender = $state;
        $this->viewToRender = $state;
        $env = Environment::get();
        $atHandle = $this->attributeType->getAttributeTypeHandle();
        $r = $env->getRecord(DIRNAME_ATTRIBUTES . '/' . $atHandle . '/' . $this->viewToRender . '.php',
            $this->attributePkgHandle);
        if ($this->initialViewToRender == 'composer' && !$r->exists()) {
            $this->viewToRender = 'form';
        }
    }

    public function startRender()
    {
        $js = $this->controller->getAttributeTypeFileURL($this->viewToRender . '.js');
        $css = $this->controller->getAttributeTypeFileURL($this->viewToRender . '.css');
        $html = Loader::helper('html');
        if ($js != false) {
            $this->addOutputAsset($html->javascript($js));
        }
        if ($css != false) {
            $this->addOutputAsset($html->css($css));
        }
    }

    public function setupRender()
    {
        $this->runControllerTask();
        $atHandle = $this->attributeType->getAttributeTypeHandle();
        $env = Environment::get();
        $r = $env->getRecord(DIRNAME_ATTRIBUTES . '/' . $atHandle . '/' . $this->viewToRender . '.php',
            $this->attributePkgHandle);
        $file = $r->file;
        $this->setViewTemplate($file);
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
        $action = $this->initialViewToRender;
        if ($action == 'composer' && !method_exists($this->controller, 'composer')) {
            $action = 'form';
        }
        $this->controller->runAction($action);
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
        @Loader::element(DIRNAME_ATTRIBUTES . '/' . $view . '_header', array('type' => $this->attributeType));
    }

    protected function onAfterGetContents()
    {
        @Loader::element(DIRNAME_ATTRIBUTES . '/' . $view . '_footer', array('type' => $this->attributeType));
    }

    public function field($fieldName)
    {
        return $this->controller->field($fieldName);
    }
}
