<?php

class LegacyErrorTest extends PHPUnit_Framework_TestCase
{
    public function testErrorMethodsBackwardCompatibility()
    {
        $e = \Core::make('error');
        $this->assertInstanceOf('JsonSerializable', $e);
        $this->assertInstanceOf('ArrayAccess', $e);
        $this->assertTrue(method_exists($e, 'getList'));
        $this->assertTrue(method_exists($e, 'add'));
        $this->assertTrue(method_exists($e, 'has'));
        $this->assertTrue(method_exists($e, 'output'));
        $this->assertTrue(method_exists($e, 'outputJSON'));
        $e1 = \Core::make('helper/validation/error');
        $this->assertEquals($e, $e1);
    }

    public function testBasicErrorFunctionality()
    {
        $e = \Core::make('error');
        $this->assertEquals(false, $e->has());
        $e->add('This is a test.');
        $this->assertEquals(1, count($e->getList()));
        $this->assertEquals(true, $e->has());
        $text = $e->getList()[0];
        $this->assertEquals('This is a test.', (string) $text);
        ob_start();
        $e->outputJSON();
        $output = ob_get_contents();
        ob_end_clean();
        ob_start();
        $e->output();
        $html = ob_get_contents();
        ob_end_clean();
        $this->assertEquals(json_encode($e), $output);
        $this->assertEquals('<ul class="ccm-error"><li>This is a test.</li></ul>', $html);
    }


}