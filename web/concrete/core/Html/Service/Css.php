<?
namespace Concrete\Core\Html\Service;
use Page;
use Less_Parser;
class Css {


	/**
	 * The output path. If not set, then it will be the same path as the less file.
	 */
	protected $fileOutputPath;

	/**
 	 * A global root to the stylesheets for this css instance.
	 * 	 */
	protected $urlRoot;

	/**
	 * When initializing the Css parser, we provide a callback that, when provided with a 
	 * segment of the file, returns the whole path to the file. This is so we can use the 
	 * environment library to provide this file, without binding the environment library to
	 * this helper.
	 * */
	protected $fileLocatorCallback;

	/**
	 * Returns a path to a compiled CSS file based on a LESS file.
	 * @param  string $file 
	 * @return string
	 */
	public function less($file, $urlRootOverride = false) {
		$outputPath = $this->fileOutputPath;
		$urlRoot = ($urlRootOverride) ? $urlRootOverride : $this->urlRoot;

		$l = new Less_Parser(array('compress' => true));
		$fullPath = $this->resolveFile($file, $urlRoot);
		$parser = $l->parseFile($fullPath);
		if (!isset($this->fileOutputPath)) {
			$outputPath = dirname($fullPath);
		}
		$transformedFile = str_replace('.less', '.css', $file);
		$outputFile = $outputPath . '/' . $transformedFile;
		file_put_contents($outputFile, $parser->getCSS());
		return rtrim($urlRoot, '/') . '/' . $transformedFile;
	}

	/**
	 * Resolves the location of the file by passing it through the locator
	 */
	public function resolveFile($file) {
		$path = call_user_func_array($this->fileLocatorCallback, array($file));
		return $path;
	}

	/**
	 * Public setter for the CSS root path.
	 */
	public function setLocator($callback) {
		$this->fileLocatorCallback = $callback;
	}

	/**
	 * Public setter for the output path of LESS files.
	 */
	public function setCompiledStylesheetOutputPath($path) {
		$this->fileOutputPath = $path;
	}

	/**
	 * Public setter for the global URL root to the stylesheet.
	 */
	public function setUrlRootToStylesheet($urlRoot) {
		$this->urlRoot = $urlRoot;
	}

}