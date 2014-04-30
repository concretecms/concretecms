<?
namespace Concrete\Core\Html\Service;
use Page;
use Less_Parser;
class Css {


    protected $compiledOutputPath;
    protected $compiledRelativePath;

    /**
     * When initializing the Css parser, we provide a callback that, when provided with a
     * segment of the file, returns the whole path to the file. This is so we can use the
     * environment library to provide this file, without binding the environment library to
     * this helper.
     * */
    protected $fileLocatorCallback;
    protected $relativeUrlRootLocatorCallback;

    /**
     * Returns a path to a compiled CSS file based on a LESS file.
     * @param  string $file
     * @return string
     */
    public function less($file) {

        $sourceFile = $this->resolveFile($file);

        $compiledOutputPath = $this->compiledOutputPath;
        if (!isset($this->compiledOutputPath)) {
            $compiledOutputPath = dirname($sourceFile);
        }

        $transformedFile = str_replace('.less', '.css', $file);
        $outputFile = $compiledOutputPath . '/' . $transformedFile;

        if (file_exists($outputFile) && file_exists($sourceFile)) {
            if (filemtime($outputFile) > filemtime($sourceFile)) {
                return rtrim($this->compiledRelativePath, '/') . '/' . $transformedFile;
            }
        }

        $l = new Less_Parser(array('compress' => true));
        $urlRoot = false;
        if (isset($this->relativeUrlRootLocatorCallback)) {
            $urlRoot = $this->resolveUrlRoot($file);
        }
        $parser = $l->parseFile($sourceFile, $urlRoot);

        if (!file_exists(dirname($outputFile))) {
            @mkdir(dirname($outputFile), DIRECTORY_PERMISSIONS_MODE, true);
        }

        file_put_contents($outputFile, $parser->getCSS());
        return rtrim($this->compiledRelativePath, '/') . '/' . $transformedFile;
    }

    /**
     * Resolves the location of the file by passing it through the locator
     */
    public function resolveFile($file) {
        $path = call_user_func_array($this->fileLocatorCallback, array($file));
        return $path;
    }

    /**
     * Resolves the location of the file by passing it through the locator
     */
    public function resolveUrlRoot($file) {
        $url = call_user_func_array($this->relativeUrlRootLocatorCallback, array($file));
        return $url;
    }

    /**
     * Public setter for the CSS root path.
     */
    public function setSourceLocator($callback) {
        $this->fileLocatorCallback = $callback;
    }

    public function setRelativeUrlRootLocator($callback) {
        $this->relativeUrlRootLocatorCallback = $callback;
    }

    public function setCompiledOutputPath($path) {
        $this->compiledOutputPath = $path;
    }

    public function setCompiledRelativePath($path) {
        $this->compiledRelativePath = $path;
    }


}