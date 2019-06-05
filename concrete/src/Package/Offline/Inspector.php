<?php

namespace Concrete\Core\Package\Offline;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;

/**
 * Class to extract information from package controller.php files.
 */
class Inspector
{
    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $fs;

    /**
     * List of registered parsers.
     *
     * @var \Concrete\Core\Package\Offline\Parser[]|null
     */
    private $parsers;

    /**
     * @param \Illuminate\Filesystem\Filesystem $fs
     * @param \Concrete\Core\Package\Offline\Parser[] $parsers
     */
    public function __construct(Filesystem $fs, array $parsers)
    {
        $this->fs = $fs;
        $this->parsers = $parsers;
    }

    /**
     * Get the parsers to be used.
     *
     * \Concrete\Core\Package\Offline\Parser[]
     */
    public function getParsers()
    {
        return $this->parsers;
    }

    /**
     * Extract the information from the controller.php file of a package.
     *
     * @param string $filename
     *
     * @throws \Concrete\Core\Package\Offline\Exception in case of problems
     *
     * @return \Concrete\Core\Package\Offline\PackageInfo
     */
    public function inspectControllerFile($filename)
    {
        try {
            $contents = $this->fs->get($filename);
        } catch (FileNotFoundException $x) {
            throw Exception::create(Exception::ERRORCODE_FILENOTFOUND, t('Unable to find the file %s', $filename), $filename);
        }
        if (!is_string($contents)) {
            throw Exception::create(Exception::ERRORCODE_FILENOTREADABLE, t('Unable to read the file %s', $filename), $filename);
        }

        return $this->inspectControllerContent($contents);
    }

    /**
     * Extract the information from the contents of the controller.php file of a package.
     *
     * @param string|mixed $content
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
