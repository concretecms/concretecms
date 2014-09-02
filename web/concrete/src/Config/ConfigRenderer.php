<?php

namespace Concrete\Core\Config;

class ConfigRenderer {

    protected $config = null;

    public function __construct(array $config) {
        $this->config = $config;
    }

    public function render($eol = PHP_EOL, $spacer = '    ') {
        return $this->renderString($this->config, $eol, $spacer);
    }

    protected function renderString($array, $eol = PHP_EOL, $spacer = '    ') {
        $rendered = $this->renderArrayRecursive($array, $spacer);
        $string = implode($eol, array_map(function($val) use ($spacer) {
            if (substr($val, strlen($val) - 1, 1) != '(') {
                return $spacer . $val . ',';
            }
            return $spacer . $val;
        }, $rendered));

        return "<?php\n\nreturn array(\n" . $string . "\n);\n";
    }

    protected function renderArrayRecursive($array, $spacer = '    ') {
        $result = array();
        $arrays = array();

        $associative = false;
        $expect = 0;
        foreach ($array as $key => $value) {
            if ($key !== $expect++) {
                $associative = true;
                break;
            }
        }

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $arrays[] = array($key, $value);
            } else if (is_scalar($value)) {
                if (is_bool($value)) {
                    $bool = $value ? 'true' : 'false';
                    if ($associative) {
                        $result[] = '"' . addslashes($key) . '" => ' . $bool;
                    } else {
                        $result[] = $bool;
                    }
                } elseif (is_string($value)) {
                    if ($associative) {
                        $result[] = '"' . addslashes($key) . '" => "' . addslashes($value) . '"';
                    } else {
                        $result[] = '"' . addslashes($value) . '"';
                    }
                } else {
                    if (is_numeric($value)) {
                        if ($associative) {
                            $result[] = '"' . addslashes($key) . '" => ' . $value;
                        } else {
                            $result[] = $value;
                        }
                    }
                }
            } else if (is_callable($value)) {
                throw new \Exception('Cannot write to config file, because it contains types that cannot be rendered.');
            }
        }

        foreach ($arrays as $array) {
            list($key, $value) = $array;
            if ($associative) {
                $result[] = '"' . addslashes($key) . '" => array(';
            } else {
                $result[] = 'array(';
            }

            foreach ($this->renderArrayRecursive($value) as $line) {
                $result[] = $spacer . $line;
            }
            $result[] = ')';
        }

        return $result;
    }

}
