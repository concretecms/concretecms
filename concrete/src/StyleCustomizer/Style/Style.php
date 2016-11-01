<?php
namespace Concrete\Core\StyleCustomizer\Style;

use Environment;

/**
 * @method static Value[] getValuesFromVariables($rules = [])
 */
abstract class Style
{
    protected $variable;
    protected $name;

    abstract public function render($value = false);
    /*
     * This is commented out only because PHP raises a "strict standards" warning,
     * but child classes MUST implement it (see also https://bugs.php.net/bug.php?id=72993 ).
     * abstract public static function getValuesFromVariables($rules = []);
     */
    abstract public function getValueFromRequest(\Symfony\Component\HttpFoundation\ParameterBag $request);

    public function getValueFromList(\Concrete\Core\StyleCustomizer\Style\ValueList $list)
    {
        $type = static::getTypeFromClass($this);
        foreach ($list->getValues() as $value) {
            if ($value->getVariable() == $this->getVariable() && $type == static::getTypeFromClass($value, 'Value')) {
                return $value;
            }
        }
    }

    protected static function getTypeFromClass($class, $suffix = 'Style')
    {
        $class = get_class($class);
        $class = substr($class, strrpos($class, '\\') + 1);
        $type = uncamelcase(substr($class, 0, strrpos($class, $suffix)));

        return $type;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    /** Returns the display name for this style (localized and escaped accordingly to $format)
     * @param string $format = 'html'
     *   Escape the result in html format (if $format is 'html').
     *   If $format is 'text' or any other value, the display name won't be escaped.
     *
     * @return string
     */
    public function getDisplayName($format = 'html')
    {
        $value = tc('StyleName', $this->getName());
        switch ($format) {
            case 'html':
                return h($value);
            case 'text':
            default:
                return $value;
        }
    }

    public function setVariable($variable)
    {
        $this->variable = $variable;
    }

    public function getVariable()
    {
        return $this->variable;
    }

    /**
     * Returns a path to an elements directory for this Style. Might not be used by all styles.
     *
     * @return string
     */
    public function getFormElementPath()
    {
        $className = implode('', array_slice(explode('\\', get_called_class()), -1));
        $segment = substr($className, 0, strpos($className, 'Style'));
        $element = uncamelcase($segment);
        $env = Environment::get();

        return $env->getPath(DIRNAME_ELEMENTS . '/' . DIRNAME_STYLE_CUSTOMIZER . '/' . DIRNAME_STYLE_CUSTOMIZER_TYPES . '/' . $element . '.php');
    }
}
