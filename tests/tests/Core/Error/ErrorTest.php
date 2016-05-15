<?php

class ErrorTest extends PHPUnit_Framework_TestCase
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

        $e = new \Concrete\Core\Error\Error();
        $this->assertInstanceOf('Concrete\Core\Error\ErrorBag\ErrorBag', $e);
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
        $this->assertInstanceOf('Concrete\Core\Error\ErrorBag\Error\Error', $text);
        ob_start();
        $e->outputJSON();
        $output = ob_get_contents();
        ob_end_clean();
        ob_start();
        $e->output();
        $html = ob_get_contents();
        ob_end_clean();
        $json = json_encode($e);
        $this->assertEquals('{"error":true,"errors":["This is a test."]}', $json);
        $this->assertEquals($json, $output);
        $output = json_decode($output);
        $this->assertEquals(true, $output->error);
        $this->assertEquals(1, count($output->errors));
        $this->assertEquals('<ul class="ccm-error"><li>This is a test.</li></ul>', $html);
    }

    public function testShorthandErrorSyntax()
    {
        $e = \Core::make('error');
        $e->add('This is a standard error', 'first_name');
        $error = $e->getList()[0];
        $this->assertInstanceOf('Concrete\Core\Error\ErrorBag\Error\Error', $error);
        $field = $error->getField();
        $this->assertInstanceOf('Concrete\Core\Error\ErrorBag\Field\Field', $field);
        $this->assertEquals('first_name', $field->getFieldElementName());
        $this->assertEquals('first_name', $field->getDisplayName());
        $this->assertEquals('This is a standard error', $error->getMessage());
        $json = json_encode($error);
        $this->assertEquals('{"message":"This is a standard error","field":{"element":"first_name","name":"first_name"}}',
            $json);
    }

    public function testFullErrorSyntax()
    {
        $e = \Core::make('error');
        $field = new \Concrete\Core\Error\ErrorBag\Field\Field('last_name', 'Last Name');
        $error = new \Concrete\Core\Error\ErrorBag\Error\Error('This is my error', $field);
        $e->add($error);
        $error = $e->getList()[0];
        $this->assertInstanceOf('Concrete\Core\Error\ErrorBag\Error\Error', $error);
        $field = $error->getField();
        $this->assertInstanceOf('Concrete\Core\Error\ErrorBag\Field\Field', $field);
        $this->assertEquals('last_name', $field->getFieldElementName());
        $this->assertEquals('Last Name', $field->getDisplayName());
        $this->assertEquals('This is my error', $error->getMessage());
    }

    public function testFieldNotFoundError()
    {
        $e = \Core::make('error');
        $field = new \Concrete\Core\Error\ErrorBag\Field\Field('last_name', 'Last Name');
        $error = new \Concrete\Core\Error\ErrorBag\Error\FieldNotPresentError($field);
        $e->add($error);
        $error = $e->getList()[0];
        $this->assertEquals('The field Last Name is required.', (string) $error);
    }


}