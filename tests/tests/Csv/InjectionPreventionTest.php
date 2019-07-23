<?php

namespace Csv;

use Concrete\Core\Csv\EscapeFormula;
use Concrete\Core\Csv\WriterFactory;
use League\Csv\Writer;

class InjectionPreventionTest extends \PHPUnit_Framework_TestCase
{

    protected $input = [
        ['=5+5'],
        ['-5+5'],
        ['+5+5'],
        ['@5+5'],
        ['%5+5'],
        ['&5+5'],
        ['5+5'],
        ['*5+5'],
        ['/5+5'],
    ];

    public function testCsvEscape()
    {
        $escaper = new EscapeFormula();

        $this->assertSame(array_map($escaper, $this->input), [
            ["\t=5+5"], // Escaped
            ["\t-5+5"], // Escaped
            ["\t+5+5"], // Escaped
            ["\t@5+5"], // Escaped
            ['%5+5'],   // Not Escaped
            ['&5+5'],   // Not Escaped
            ['5+5'],    // Not Escaped
            ['*5+5'],   // Not Escaped
            ['/5+5'],   // Not Escaped
        ]);
    }

    public function testLeagueCsvUsage()
    {
        $escaper = new EscapeFormula();

        $stream = tmpfile();
        $writer = Writer::createFromStream($stream);
        $writer->addFormatter($escaper);

        // Write all the input items to the stream
        array_map([$writer, 'insertOne'], $this->input);

        rewind($stream);
        while ($row = fgets($stream)) {
            $result[] = [trim($row, PHP_EOL)];
        }

        $this->assertSame([
            ["\"\t=5+5\""], // Escaped
            ["\"\t-5+5\""], // Escaped
            ["\"\t+5+5\""], // Escaped
            ["\"\t@5+5\""], // Escaped
            ['%5+5'],   // Not Escaped
            ['&5+5'],   // Not Escaped
            ['5+5'],    // Not Escaped
            ['*5+5'],   // Not Escaped
            ['/5+5'],   // Not Escaped
        ], $result);
    }

    public function testWriterFactory()
    {
        $factory = \Core::make(WriterFactory::class);

        $writer = $factory->createFromString('');
        $writer->insertAll($this->input);

        $stream = $writer->getIterator();
        $stream->rewind();

        $result = iterator_to_array($stream);
        $this->assertEquals([
            ["\t=5+5"], // Escaped
            ["\t-5+5"], // Escaped
            ["\t+5+5"], // Escaped
            ["\t@5+5"], // Escaped
            ['%5+5'],   // Not Escaped
            ['&5+5'],   // Not Escaped
            ['5+5'],    // Not Escaped
            ['*5+5'],   // Not Escaped
            ['/5+5'],   // Not Escaped
        ], $result);
    }

}
