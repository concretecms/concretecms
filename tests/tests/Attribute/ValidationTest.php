<?php

namespace Concrete\Tests\Attribute;

use PHPUnit_Framework_TestCase;

class ValidationTest extends PHPUnit_Framework_TestCase
{
    public function testAttributeKeyErrorFunctionality()
    {
        $settings = new \Concrete\Core\Entity\Attribute\Key\Settings\TextSettings();
        $key = new \Concrete\Core\Entity\Attribute\Key\UserKey();
        $key->setAttributeKeyID(1);
        $key->setAttributeKeyName('First Name');
        $key->setAttributeKeySettings($settings);
        $controller = \Core::make('\Concrete\Attribute\Text\Controller');
        $controller->setAttributeKey($key);

        $validator = $controller->getValidator();
        $this->assertInstanceOf('\Concrete\Core\Attribute\StandardValidator', $validator);

        $request = new \Symfony\Component\HttpFoundation\Request();
        $response = $validator->validateSaveValueRequest($controller, $request);

        $error = $response->getErrorObject();
        $this->assertFalse($response->isValid());
        $this->assertTrue($error->has());
        $errors = $error->getList();
        $this->assertInstanceOf('\Concrete\Core\Error\ErrorList\Error\FieldNotPresentError', $errors[0]);
        $this->assertEquals('The field First Name is required.', (string) $errors[0]);

        $post['akID'][1]['value'] = 'Oh hai!';

        $request = new \Symfony\Component\HttpFoundation\Request([], $post);
        $response = $validator->validateSaveValueRequest($controller, $request);
        $error = $response->getErrorObject();
        $this->assertTrue($response->isValid());
        $this->assertFalse($error->has());
    }

    public function testEmailAddressAttribute()
    {
        // Testing a missing attribute and an attribute with an error.
        $settings = new \Concrete\Core\Entity\Attribute\Key\Settings\TextSettings();
        $key = new \Concrete\Core\Entity\Attribute\Key\UserKey();
        $key->setAttributeKeyID(1);
        $key->setAttributeKeyName('Email Address');
        $key->setAttributeKeySettings($settings);
        $controller = \Core::make('\Concrete\Attribute\Email\Controller');
        $controller->setAttributeKey($key);

        $validator = $controller->getValidator();
        $this->assertInstanceOf('\Concrete\Core\Attribute\StandardValidator', $validator);

        $post['akID'][1]['value'] = '';
        $request = new \Symfony\Component\HttpFoundation\Request([], $post);

        $response = $validator->validateSaveValueRequest($controller, $request);
        $error = $response->getErrorObject();
        $this->assertFalse($response->isValid());
        $this->assertTrue($error->has());
        $errors = $error->getList();
        $this->assertInstanceOf('\Concrete\Core\Error\ErrorList\Error\FieldNotPresentError', $errors[0]);
        $this->assertEquals('The field Email Address is required.', (string) $errors[0]);
        $this->assertEquals(1, count($errors));

        $post['akID'][1]['value'] = 'foo';
        $request = new \Symfony\Component\HttpFoundation\Request([], $post);

        $response = $validator->validateSaveValueRequest($controller, $request);
        $error = $response->getErrorObject();
        $this->assertFalse($response->isValid());
        $this->assertTrue($error->has());
        $errors = $error->getList();
        $this->assertInstanceOf('\Concrete\Core\Error\ErrorList\Error\Error', $errors[0]);
        $this->assertEquals('The email address "foo" is not valid.', (string) $errors[0]);
        $this->assertEquals(1, count($errors));

        $post['akID'][1]['value'] = 'test@test.com';
        $request = new \Symfony\Component\HttpFoundation\Request([], $post);

        $response = $validator->validateSaveValueRequest($controller, $request);
        $error = $response->getErrorObject();
        $this->assertTrue($response->isValid());
        $this->assertFalse($error->has());
    }

    public function testAddress()
    {
        $settings = new \Concrete\Core\Entity\Attribute\Key\Settings\AddressSettings();
        $key = new \Concrete\Core\Entity\Attribute\Key\UserKey();
        $key->setAttributeKeyID(1);
        $key->setAttributeKeyName('Contact Address');
        $key->setAttributeKeySettings($settings);
        $controller = \Core::make('\Concrete\Attribute\Address\Controller');
        $controller->setAttributeKey($key);
        $validator = $controller->getValidator();

        $request = new \Symfony\Component\HttpFoundation\Request();
        $response = $validator->validateSaveValueRequest($controller, $request);
        $error = $response->getErrorObject();
        $this->assertFalse($response->isValid());
        $this->assertTrue($error->has());
        $errors = $error->getList();
        $this->assertInstanceOf('\Concrete\Core\Error\ErrorList\Error\FieldNotPresentError', $errors[0]);
        $this->assertEquals('The field Contact Address is required.', (string) $errors[0]);
        $this->assertEquals(1, count($errors));

        $post['akID'][1]['address1'] = '123 SW Test';
        $post['akID'][1]['city'] = 'Portland';
        $post['akID'][1]['state_province'] = 'OR';
        $post['akID'][1]['country'] = 'US';
        $post['akID'][1]['postal_code'] = '11111';
        $request = new \Symfony\Component\HttpFoundation\Request([], $post);

        $response = $validator->validateSaveValueRequest($controller, $request);
        $error = $response->getErrorObject();
        $this->assertTrue($response->isValid());
        $this->assertFalse($error->has());
    }

    public function testBoolean()
    {
        $settings = new \Concrete\Core\Entity\Attribute\Key\Settings\BooleanSettings();
        $key = new \Concrete\Core\Entity\Attribute\Key\UserKey();
        $key->setAttributeKeyID(1);
        $key->setAttributeKeyName('Is Featured');
        $key->setAttributeKeySettings($settings);
        $controller = \Core::make('\Concrete\Attribute\Boolean\Controller');
        $controller->setAttributeKey($key);
        $validator = $controller->getValidator();

        $request = new \Symfony\Component\HttpFoundation\Request();
        $response = $validator->validateSaveValueRequest($controller, $request);
        $error = $response->getErrorObject();
        $this->assertFalse($response->isValid());
        $this->assertTrue($error->has());
        $errors = $error->getList();
        $this->assertInstanceOf('\Concrete\Core\Error\ErrorList\Error\FieldNotPresentError', $errors[0]);
        $this->assertEquals('The field Is Featured is required.', (string) $errors[0]);
        $this->assertEquals(1, count($errors));

        $post['akID'][1]['value'] = '1';
        $request = new \Symfony\Component\HttpFoundation\Request([], $post);

        $response = $validator->validateSaveValueRequest($controller, $request);
        $error = $response->getErrorObject();
        $this->assertTrue($response->isValid());
        $this->assertFalse($error->has());
    }

    public function testDateTime()
    {
        $settings = new \Concrete\Core\Entity\Attribute\Key\Settings\BooleanSettings();
        $key = new \Concrete\Core\Entity\Attribute\Key\UserKey();
        $key->setAttributeKeyID(1);
        $key->setAttributeKeyName('Is Featured');
        $key->setAttributeKeySettings($settings);
        $controller = \Core::make('\Concrete\Attribute\Boolean\Controller');
        $controller->setAttributeKey($key);
        $validator = $controller->getValidator();

        $request = new \Symfony\Component\HttpFoundation\Request();
        $response = $validator->validateSaveValueRequest($controller, $request);
        $error = $response->getErrorObject();
        $this->assertFalse($response->isValid());
        $this->assertTrue($error->has());
        $errors = $error->getList();
        $this->assertInstanceOf('\Concrete\Core\Error\ErrorList\Error\FieldNotPresentError', $errors[0]);
        $this->assertEquals('The field Is Featured is required.', (string) $errors[0]);
        $this->assertEquals(1, count($errors));

        $post['akID'][1]['value'] = '1';
        $request = new \Symfony\Component\HttpFoundation\Request([], $post);

        $response = $validator->validateSaveValueRequest($controller, $request);
        $error = $response->getErrorObject();
        $this->assertTrue($response->isValid());
        $this->assertFalse($error->has());
    }
}
