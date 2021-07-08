<?php
namespace Concrete\Core\StyleCustomizer\Adapter;

use Concrete\Core\StyleCustomizer\Normalizer\NormalizerInterface;
use Concrete\Core\StyleCustomizer\Processor\ProcessorInterface;
use Concrete\Core\StyleCustomizer\Skin\SkinInterface;

class LessAdapter implements AdapterInterface
{

    public function getVariablesFile(SkinInterface $skin): string
    {
        throw new \Exception('Not implemented yet.');
    }

    public function getVariableNormalizer(): NormalizerInterface
    {
        throw new \Exception('Not implemented yet.');
    }

    public function getProcessor(): ProcessorInterface
    {
        throw new \Exception('Not implemented yet.');
    }

    public function getEntrypointFile(SkinInterface $skin): string
    {
        throw new \Exception('Not implemented yet.');
    }

}
