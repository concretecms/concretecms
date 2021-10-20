<?php

namespace Concrete\Core\StyleCustomizer\Normalizer;
use Concrete\Core\StyleCustomizer\Normalizer\Legacy\ImageVariable;

/**
 * Class LegacyNormalizer
 *
 * We need this class because some legacy approaches should still be supported, but they aren't the right things to do.
 * 1. Image backgrounds, etc... things with URL need to include their url(...) as part of the variable. But old themes
 * don't do this, so we we're going to sniff those variables that end with `-image` and force those variables to be
 * image variables.
 */
class LegacyNormalizer extends LessNormalizer
{

    public function createVariableCollectionFromFile(string $variablesFilePath): NormalizedVariableCollection
    {
        $collection = parent::createVariableCollectionFromFile($variablesFilePath);

        foreach($collection->getValues() as $offset => $variable) {

            // Let's not include the special preset functions because the Less compiler itself doesn't like them
            if (in_array($variable->getName(), ['preset-name', 'preset-icon'])) {
                $collection->offsetUnset($offset);
            }
            if ($variable->getName() === 'preset-fonts-file') {
                // Don't ask me why we have to do this. The 8.5.x and earlier variable collection surrounds
                // this value in quotes, and it works. Without this code here the substitution doesn't work.
                $collection->set($offset, new Variable($variable->getName(), '"' . $variable->getValue() . '"'));
            }
            // Now let's iterate through and if a variable ends with `-image` let's fix it.
            if (str_ends_with($variable->getName(), '-image')) {
                $collection->set($offset, new ImageVariable($this->normalizeVariableName($variable->getName()), $variable->getValue()));
            }
        }

        return $collection;
    }
}
