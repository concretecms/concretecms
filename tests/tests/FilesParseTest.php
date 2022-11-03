<?php

namespace Concrete\Tests;

use Doctrine\Common\Annotations\PhpParser;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PhpParser\Node\Stmt;

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
            /** @var \PhpParser\Node[] $tree */
            $tree = $this->parser->parse(file_get_contents($file));

            // Traverse the tree and look for scalar type hints
            $iterate = [$tree];
            while (is_array($nodes = array_shift($iterate))) {
                /** @var \PhpParser\Node $node */
                foreach ($nodes as $node) {
                    $type = $node->getType();
                    if (
                        $node instanceof Stmt\Namespace_
                    ) {
                        $iterate[] = $node->stmts;
                        continue;
                    }

                    if ($node instanceof Stmt\Class_) {
                        $iterate[] = $node->getMethods();
                        continue;
                    }

                    if (
                        $node instanceof Stmt\Function_ ||
                        $node instanceof Stmt\ClassMethod
                    ) {
                        $iterate[] = $node->getParams();
                        continue;
                    }

                    // Check for scalar type hints
                    if ($node instanceof \PhpParser\Node\Param) {
                        // If the parameter has no type
                        if (!$node->type) {
                            continue;
                        }

                        $paramType = is_string($node->type) ? $node->type : $node->type->toString();
                        switch ($paramType) {
                            case 'string':
                            case 'int':
                            case 'bool':
                            case 'float':
                                return $this->fail('Scalar type hints detected: ' . $name . ':' . $node->getLine());
                        }
                    }
                }
            }
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
