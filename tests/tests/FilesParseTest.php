<?php

namespace Concrete\Tests;

use PhpParser\ParserFactory;

class FilesParseTest extends \PHPUnit_Framework_TestCase
{

    /** @var \PhpParser\Parser\Php5 */
    private $parser;

    protected function setUp()
    {
        $factory = new ParserFactory();
        $this->parser = $factory->create($factory::ONLY_PHP5);
    }

    /**
     * @dataProvider phpFiles
     */
    public function testSupportsPhp5($name, $file)
    {
        try {
            $this->parser->parse(file_get_contents($file));
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to parse file: .' . $name, 0, $e);
        }
    }

    public function phpFiles()
    {
        $skipPaths = [
            '~^/concrete/vendor/~',
        ];

        /** @var \SplFileInfo $file */
        $basePath = realpath(__DIR__ . '/../../');
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($basePath . '/concrete')) as $file) {
            if ($file->getExtension() === 'php') {
                $relativePath = substr($file->getRealPath(), strlen($basePath));

                // Skip vendor files
                $skip = false;
                foreach ($skipPaths as $regex) {
                    if (preg_match($regex, $relativePath)) {
                        $skip = true;
                        break;
                    }
                }

                if ($skip) {
                    continue;
                }

                yield [$relativePath, $file->getPathname()];
            }
        }
    }

}
