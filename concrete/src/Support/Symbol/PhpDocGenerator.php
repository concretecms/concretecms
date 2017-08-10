<?php
namespace Concrete\Core\Support\Symbol;

use ReflectionClass;

/**
 * Helper class to generate PHPDoc.
 */
class PhpDocGenerator
{
    /**
     * Are we generating PHPDoc to be placed inside a namespace?
     *
     * @var bool
     */
    protected $insideNamespace = false;

    /**
     * Are we generating PHPDoc to be placed inside a namespace?
     *
     * @return bool
     */
    public function isInsideNamespace()
    {
        return $this->insideNamespace;
    }

    /**
     * Are we generating PHPDoc to be placed inside a namespace?
     *
     * @param bool $value
     *
     * @return $this
     */
    public function setIsInsideNamespace($value)
    {
        $this->insideNamespace = (bool) $value;

        return $this;
    }

    /**
     * Intentation.
     *
     * @var string
     */
    protected $indentation = '';

    /**
     * Get the indentation.
     *
     * @return string
     */
    public function getIntentation()
    {
        return $this->indentation;
    }

    /**
     * Set the indentation.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setIntentation($value)
    {
        $this->indentation = (string) $value;

        return $this;
    }

    /**
     * Generate the PHPDoc to describe a variable.
     *
     * @param string $name The variable name
     * @param mixed $value The variable value
     *
     * @return string
     */
    public function describeVar($name, $value)
    {
        return $this->indentation . '/* @var ' . $this->getVarType($value) . ' ' . ($name[0] === '$' ? '' : '$') . $name . " */\n";
    }

    /**
     * Generate the PHPDoc to describe a list of variables.
     *
     * @param array $vars
     * @param bool $sortByName
     *
     * @return string
     */
    public function describeVars(array $vars, $sortByName = true)
    {
        $result = '';
        if ($sortByName) {
            ksort($vars, SORT_NATURAL);
        }
        foreach ($vars as $name => $value) {
            $result .= $this->describeVar($name, $value);
        }

        return $result;
    }

    /**
     * Get the PHPDoc type name of a variable.
     *
     * @param mixed $var
     * @param int $arrayLevel
     *
     * @return string
     */
    protected function getVarType($var, $arrayLevel = 0)
    {
        $phpType = gettype($var);
        switch ($phpType) {
            case 'boolean':
                $result = 'bool';
                break;
            case 'integer':
                $result = 'int';
                break;
            case 'double':
                $result = 'float';
                break;
            case 'string':
                $result = 'string';
                break;
            case 'array':
                if ($arrayLevel > 1) {
                    $result = 'array';
                } else {
                    $result = null;
                    $first = true;
                    foreach ($var as $item) {
                        $itemType = $this->getVarType($item, $arrayLevel + 1);
                        if ($first) {
                            $result = $itemType;
                            $commonObjectDescriptors = $this->getObjectDescriptors($item);
                            $first = false;
                        } else {
                            if ($result !== $itemType) {
                                $result = null;
                                if (empty($commonObjectDescriptors)) {
                                    break;
                                }
                                if (!empty($commonObjectDescriptors)) {
                                    $commonObjectDescriptors = array_intersect($commonObjectDescriptors, $this->getObjectDescriptors($item));
                                }
                            }
                        }
                    }
                    if ($result !== null) {
                        $result .= '[]';
                    } elseif (!empty($commonObjectDescriptors)) {
                        $result = array_shift($commonObjectDescriptors) . '[]';
                    } else {
                        $result = 'array';
                    }
                }
                break;
            case 'object':
                $result = get_class($var);
                if ($result === false) {
                    $result = 'mixed';
                } else {
                    if ($this->insideNamespace) {
                        $result = '\\' . $result;
                    }
                }
                break;
            case 'resource':
                $result = 'resource';
                break;
            case 'NULL':
                $result = 'null';
                break;
            case 'unknown type':
            default:
                $result = 'mixed';
                break;
        }

        return $result;
    }

    /**
     * Get all the names representing an object instance (class name, parent class names, interface names).
     *
     * @param mixed $var
     *
     * @return array
     */
    protected function getObjectDescriptors($var)
    {
        $result = [];
        $className = is_object($var) ? get_class($var) : false;
        if ($className !== false) {
            $result[] = ($this->insideNamespace ? '\\' : '') . $className;
            $class = new ReflectionClass($className);
            for ($childClass = $class->getParentClass(); $childClass; $childClass = $childClass->getParentClass()) {
                $result[] = ($this->insideNamespace ? '\\' : '') . $childClass->getName();
            }
            foreach ($class->getInterfaceNames() as $interfaceName) {
                $result[] = ($this->insideNamespace ? '\\' : '') . $interfaceName;
            }
        }

        return $result;
    }
}
