<?php
namespace Concrete\Core\StyleCustomizer\Compiler;

use Concrete\Core\StyleCustomizer\Adapter\AdapterInterface;
use Concrete\Core\StyleCustomizer\Normalizer\NormalizedVariableCollection;
use Concrete\Core\StyleCustomizer\Skin\SkinInterface;
use Illuminate\Filesystem\Filesystem;

/**
 * Responsible for taking a theme's skin, combining it with custom variables from a request, and compiling it to
 * a temporary CSS file that can be included in a Page preview request.
 */
class PreviewRequestCompiler
{

    /**
     * @var Filesystem
     */
    protected $filesystem;

    public function compile(AdapterInterface $adapter, SkinInterface $skin, NormalizedVariableCollection $collection): string
    {
        $processor = $adapter->getProcessor();
        $file = $adapter->getEntrypointFile($skin);
        $css = $processor->compileFileToString($file, $collection);
        return $css;
    }


}
