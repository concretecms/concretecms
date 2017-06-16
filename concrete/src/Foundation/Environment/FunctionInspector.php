<?php
namespace Concrete\Core\Foundation\Environment;

use Traversable;

class FunctionInspector
{
    /**
     * List of system-level disabled functions.
     *
     * @var string[]|null
     */
    protected $disabledFunctions;

    /**
     * Set the system-level disabled functions.
     *
     * @param string[]|Traversable $functionNames
     *
     * @return $this
     */
    public function setDisabledFunctions($functionNames)
    {
        $normalized = [];
        if (is_array($functionNames) || $functionNames instanceof Traversable) {
            foreach ($functionNames as $functionName) {
                if (is_string($functionName)) {
                    $functionName = trim($functionName);
                    if ($functionName !== '') {
                        $normalized[] = strtolower($functionName);
                    }
                }
            }
        }
        $this->disabledFunctions = $normalized;

        return $this;
    }

    /**
     * Set the system-level disabled functions.
     *
     * @return string[]
     */
    public function getDisabledFunctions()
    {
        if ($this->disabledFunctions === null) {
            $iniValue = @ini_get('disable_functions');
            $this->setDisabledFunctions(is_string($iniValue) ? explode(',', $iniValue) : []);
        }

        return $this->disabledFunctions;
    }

    /**
     * Check if a function exists and is not disabled.
     *
     * @param string $functionName
     *
     * @return bool
     */
    public function functionAvailable($functionName)
    {
        $result = false;
        if (is_string($functionName)) {
            $functionName = trim($functionName);
            if ($functionName !== '') {
                if (function_exists($functionName)) {
                    $disabledFunctions = $this->getDisabledFunctions();
                    if (!in_array(strtolower($functionName), $disabledFunctions, true)) {
                        $result = true;
                    }
                }
            }
        }

        return $result;
    }
}
