<?php
namespace Concrete\Core\Support\Symbol\ClassSymbol\MethodSymbol;

class MethodSymbol {

    /**
     * @var \ReflectionMethod
     */
    protected $reflectionMethod;

    /**
     * The method handle.
     * @var string
     */
    protected $handle;

    /**
     * The parameters.
     * @var array
     */
    protected $parameters = array();

    /**
     * The docblock
     * @var string
     */
    protected $comment;

    /**
     * @param \ReflectionMethod $method
     */
    public function __construct(\ReflectionMethod $method) {
        $this->reflectionMethod = $method;
        $this->parameters = $method->getParameters();
        $this->handle = $method->getName();
    }

    /**
     * Render the Method
     * @return string
     */
    public function render() {
        $method = $this->reflectionMethod;
        $rendered = "\n" . implode("\n", array_map(trim, explode("\n", $method->getDocComment()))) . "\n";
        $visibility = \Reflection::getModifierNames($method->getModifiers());
        $visibility[] = 'static';
        $rendered .= implode(' ', array_unique($visibility)) . ' function ' . $this->handle . '(';

        $params = array();
        $calling_params = array();
        foreach($this->parameters as $parameter) {
            $param = '';

            if ($parameter->isArray()) {
                $param .= 'array ';
            } /*else if ($parameter->isCallable()) { // This should be enabled for php 5.4
                $param .= 'callable ';
            } */else if ($parameter->getClass()) {
                $param .= $parameter->getClass()->getName() . ' ';
            }
            if ($parameter->isPassedByReference()) {
                $param .= "&";
            }
            $param .= '$' . $parameter->getName();

            if ($parameter->isOptional()) {
                if ($parameter->getDefaultValue()) {
                    $default = $parameter->getDefaultValue();
                    if (!is_numeric($default)) {
                        $param .= ' = "' . $default . '"';
                    } else {
                        $param .= ' = ' . $default;
                    }
                } else {
                    $param .= ' = ""';
                }
            }

            $params[] = $param;
            $calling_params[] = '$' . $parameter->getName();
        }
        $rendered .= implode(', ', $params) . ")\n{\n";
        $rendered .= '    // ' . $method->getDeclaringClass()->getName() . '::' . $method->getName() . "();\n";
        $rendered .= '    ' .
            $method->getDeclaringClass()->getName() .
            '::' .
            $method->getName() .
            '(' .
            implode(', ', $calling_params) .
            ');';

        $rendered .= "\n}\n";
        return $rendered;
    }


}
