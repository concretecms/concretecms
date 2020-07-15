<?php

namespace Concrete\Core\Package\Offline;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;

/**
 * Class that extracts information from package controller.php files.
 */
class Inspector
{
    /**
     * The Filesystem instance to be used for filesystem operations.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $fs;

    /**
     * The list of registered parsers.
     *
     * @var \Concrete\Core\Package\Offline\Parser[]|null
     */
    private $parsers = [];

    /**
     * @param \Illuminate\Filesystem\Filesystem $fs
     * @param \Concrete\Core\Package\Offline\Parser[] $parsers
     */
    public function __construct(Filesystem $fs, array $parsers)
    {
        $this->fs = $fs;
        foreach ($parsers as $parser) {
            $this->addParser($parser);
        }
    }

    /**
     * Add a parser to the parsers list.
     *
     * @param \Concrete\Core\Package\Offline\Parser $parser
     *
     * @return $this
     */
    public function addParser(Parser $parser)
    {
        $this->parsers[] = $parser;

        return $this;
    }

    /**
     * Get the parsers list.
     *
     * \Concrete\Core\Package\Offline\Parser[]
     */
    public function getParsers()
    {
        return $this->parsers;
    }

    /**
     * Extract the package details from the directory containing the package controller.php file.
     *
     * @param string $directory the directory containing the package controller.php file
     *
     * @throws \Concrete\Core\Package\Offline\Exception in case of problems
     *
     * @return \Concrete\Core\Package\Offline\PackageInfo
     */
    public function inspectPackageDirectory($directory)
    {
        $path = @realpath($directory);
        if (!is_string($path)) {
            throw Exception::create(Exception::ERRORCODE_DIRECTORYNOTFOUND, t('Unable to find the directory %s', $directory), $directory);
        }
        $controller = rtrim(str_replace(DIRECTORY_SEPARATOR, '/', $directory), '/') . '/' . FILENAME_PACKAGE_CONTROLLER;

        return $this->inspectControllerFile($controller);
    }

    /**
     * Extract the package details reading its controller.php file.
     *
     * @param string $filename the path to the package controller.php file
     *
     * @throws \Concrete\Core\Package\Offline\Exception in case of problems
     *
     * @return \Concrete\Core\Package\Offline\PackageInfo
     */
    public function inspectControllerFile($filename)
    {
        $path = @realpath($filename);
        if (!is_string($path)) {
            throw Exception::create(Exception::ERRORCODE_FILENOTFOUND, t('Unable to find the file %s', $filename), $filename);
        }
        $path = str_replace(DIRECTORY_SEPARATOR, '/', $path);
        try {
            $contents = $this->fs->get($path);
        } catch (FileNotFoundException $x) {
            throw Exception::create(Exception::ERRORCODE_FILENOTFOUND, t('Unable to find the file %s', $filename), $filename);
        }
        if (!is_string($contents)) {
            throw Exception::create(Exception::ERRORCODE_FILENOTREADABLE, t('Unable to read the file %s', $filename), $filename);
        }

        return $this->inspectControllerContent($contents)
            ->setPackageDirectory(substr($path, 0, strrpos($path, '/')));
    }

    /**
     * Extract the package details analyzing the contents of its controller.php file.
     *
     * @param string|mixed $content the content of the package controller.php file
     *
     * @throws \Concrete\Core\Package\Offline\Exception in case of problems
     *
     * @return \Concrete\Core\Package\Offline\PackageInfo
     */
    public function inspectControllerContent($content)
    {
        if (!is_string($content)) {
            throw Exception::create(Exception::ERRORCODE_BADPARAM, t('The function %s requires a string', __METHOD__), __METHOD__);
        }
        $tokens = token_get_all($content);
        $parser = $this->determineParser($tokens);

        return $parser->extractInfo($tokens);
    }

    /**
     * Analyze the tokens to determine the analyzer to be used.
     *
     * @param array $tokens
     *
     * @throws \Concrete\Core\Package\Offline\Exception in case of problems
     *
     * @return \Concrete\Core\Package\Offline\Parser
     */
    protected function determineParser(array $tokens)
    {
        $result = null;
        foreach ($this->getParsers() as $parser) {
            if ($parser->canParseTokens($tokens)) {
                if ($result !== null) {
                    throw Exception::create(Exception::ERRORCODE_MULTIPLEPARSERSFOUND, t('Multiple parsers found'));
                }
                $result = $parser;
            }
        }
        if ($result === null) {
            throw Exception::create(Exception::ERRORCODE_NOPARSERSFOUND, t('No parsers found'));
        }

        return $result;
    }
}
