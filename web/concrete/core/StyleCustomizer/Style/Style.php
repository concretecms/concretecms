<?php
namespace Concrete\Core\StyleCustomizer\Style;
use Environment;
abstract class Style {

    protected $variable;
    protected $name;

    abstract public function render($value = false);
    abstract public function getValueFromList(\Concrete\Core\StyleCustomizer\Style\ValueList $list);
    abstract public function getValueFromRequest(\Symfony\Component\HttpFoundation\ParameterBag $request);

    public function setName($name) {
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }

    public function setVariable($variable) {
        $this->variable = $variable;
    }

    public function getVariable() {
        return $this->variable;
    }

    /**
     * Returns a path to an elements directory for this Style. Might not be used by all styles.
     * @return string
     */
    public function getFormElementPath() {
        $className = join('', array_slice(explode('\\', get_called_class()), -1));
        $segment = substr($className, 0, strpos($className, 'Style'));
        $element = uncamelcase($segment);
        $env = Environment::get();
        return $env->getPath(DIRNAME_ELEMENTS . '/' . DIRNAME_STYLE_CUSTOMIZER . '/' . DIRNAME_STYLE_CUSTOMIZER_TYPES . '/' . $element . '.php');
    }


}