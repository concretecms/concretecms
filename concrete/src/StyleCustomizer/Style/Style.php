<?php

namespace Concrete\Core\StyleCustomizer\Style;

use Concrete\Core\Filesystem\FileLocator;
use Concrete\Core\Support\Facade\Application;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * @method static \Concrete\Core\StyleCustomizer\Style\Value\Value[] getValuesFromVariables($rules = [])
 */
abstract class Style
{
    /**
     * The name of this style.
     *
     * @var string
     */
    protected $name = '';

    /**
     * The name of the associated CSS variable.
     *
     * @var string
     */
    protected $variable = '';

    /**
     * Render the control of this style.
     *
     * @param \Concrete\Core\StyleCustomizer\Style\Value\Value|null|false $value the current style value
     */
    abstract public function render($value = false);

    /*
     * This is commented out only because PHP raises a "strict standards" warning for PHP prior to version 7.0,
     * but child classes MUST implement it (see also https://bugs.php.net/bug.php?id=72993 )
     */
    // abstract public static function getValuesFromVariables($rules = []);

    /**
     * Get the value of this style as received from a request.
     *
     * @param \Symfony\Component\HttpFoundation\ParameterBag $request the received data
     *
     * @return \Concrete\Core\StyleCustomizer\Style\Value\Value|null
     */
    abstract public function getValueFromRequest(ParameterBag $request);

    /**
     * Get the value of this style extracted from a list of values.
     *
     * @param \Concrete\Core\StyleCustomizer\Style\ValueList $list
     *
     * @return \Concrete\Core\StyleCustomizer\Style\Value\Value|null
     */
    public function getValueFromList(ValueList $list)
    {
        $type = static::getTypeFromClass($this);
        foreach ($list->getValues() as $value) {
            if ($value->getVariable() == $this->getVariable() && $type == static::getTypeFromClass($value, 'Value')) {
                return $value;
            }
        }
    }

    /**
     * Get the type handle of a given Style instance.
     *
     * @param \Concrete\Core\StyleCustomizer\Style\Style|object $class
     * @param string $suffix
     *
     * @return string
     */
    protected static function getTypeFromClass($class, $suffix = 'Style')
    {
        $class = get_class($class);
        $class = substr($class, strrpos($class, '\\') + 1);
        $type = uncamelcase(substr($class, 0, strrpos($class, $suffix)));

        return $type;
    }

    /**
     * Set the name of this style.
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = (string) $name;

        return $this;
    }

    /**
     * Get the name of this style.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the display name for this style (localized and escaped accordingly to $format).
     *
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

    /**
     * Set the name of the associated CSS variable.
     *
     * @param string $variable
     *
     * @return $this
     */
    public function setVariable($variable)
    {
        $this->variable = (string) $variable;

        return $this;
    }

    /**
     * Get the name of the associated CSS variable.
     *
     * @return string
     */
    public function getVariable()
    {
        return $this->variable;
    }

    /**
     * Get a path to an elements directory for this Style. Might not be used by all styles.
     *
     * @return string
     */
    public function getFormElementPath()
    {
        $app = Application::getFacadeApplication();
        $className = implode('', array_slice(explode('\\', get_called_class()), -1));
        $segment = substr($className, 0, strpos($className, 'Style'));
        $element = uncamelcase($segment);
        $locator = $app->make(FileLocator::class);
        $record = $locator->getRecord(DIRNAME_ELEMENTS . '/' . DIRNAME_STYLE_CUSTOMIZER . '/' . DIRNAME_STYLE_CUSTOMIZER_TYPES . '/' . $element . '.php');

        return $record->getFile();
    }
}
