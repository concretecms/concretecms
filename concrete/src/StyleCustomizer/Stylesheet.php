<?php
namespace Concrete\Core\StyleCustomizer;

use Concrete\Core\StyleCustomizer\Normalizer\NormalizedVariableCollection;
use Config;

/**
 * @deprecated. Use skins instead.
 */
class Stylesheet
{
    protected $file; // full path to stylesheet e.g. /full/path/to/concrete/themes/greek_yogurt/css/main.less
    protected $sourceUriRoot; // root of source. e.g. /concrete/themes/greek_yogurt/
    protected $outputDirectory; // e.g /full/path/to/files/cache/themes/greek_yogurt/css/main.css"
    protected $relativeOutputDirectory; // e.g /files/cache/themes/greek_yogurt/css/main.css"
    protected $stylesheet; // e.g "css/main.less";

    /**
     * @var NormalizedVariableCollection
     */
    protected $variableCollection;

    public function __construct($stylesheet, $file, $sourceUriRoot, $outputDirectory, $relativeOutputDirectory)
    {
        $this->stylesheet = $stylesheet;
        $this->file = $file;
        $this->sourceUriRoot = $sourceUriRoot;
        $this->outputDirectory = $outputDirectory;
        $this->relativeOutputDirectory = $relativeOutputDirectory;
    }

    public function setVariableCollection(NormalizedVariableCollection $variableCollection)
    {
        $this->variableCollection = $variableCollection;
    }
    /**
     * Compiles the stylesheet using LESS. If a ValueList is provided they are
     * injected into the stylesheet.
     *
     * @return string CSS
     */
    public function getCss()
    {
        $parser = new \Less_Parser(
            array(
                'cache_dir' => Config::get('concrete.cache.directory'),
                'compress' => (bool) Config::get('concrete.theme.compress_preprocessor_output'),
                'sourceMap' => !Config::get('concrete.theme.compress_preprocessor_output') && (bool) Config::get('concrete.theme.generate_less_sourcemap'),
            )
        );
        $parser = $parser->parseFile($this->file, $this->sourceUriRoot);
        if (isset($this->variableCollection) && $this->variableCollection instanceof NormalizedVariableCollection) {
            $variables = [];
            foreach ($this->variableCollection->getValues() as $variable) {
                $variables[$variable->getName()] = (string) $variable->getValue();
            }
            $parser->ModifyVars($variables);
        }
        $css = $parser->getCss();
        return $css;
    }

    public function output()
    {
        $css = $this->getCss();
        $path = dirname($this->getOutputPath());
        if (!file_exists($path)) {
            @mkdir($path, Config::get('concrete.filesystem.permissions.directory'), true);
        }
        file_put_contents($this->getOutputPath(), $css);
    }

    public function clearOutputFile()
    {
        $filename = $this->getOutputPath();
        if (file_exists($filename)) {
            @unlink($filename);
        }
    }

    public function outputFileExists()
    {
        return file_exists($this->getOutputPath());
    }

    public function getOutputPath()
    {
        return $this->outputDirectory . '/' . str_replace('.less', '.css', $this->stylesheet);
    }

    public function getOutputRelativePath()
    {
        return $this->relativeOutputDirectory . '/' . str_replace('.less', '.css', $this->stylesheet);
    }
}
