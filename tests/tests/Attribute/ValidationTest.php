<?php

class ValidationTest extends PHPUnit_Framework_TestCase
{

    public function testAttributeKeyErrorFunctionality()
    {
        $key_type = new \Concrete\Core\Entity\Attribute\Key\Type\TextType();
        $key = new \Concrete\Core\Entity\Attribute\Key\UserKey();
        $key->setAttributeKeyID(1);
        $key->setAttributeKeyName('First Name');
        $key->setAttributeKeyType($key_type);
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
        $this->assertInstanceOf('\Concrete\Core\Error\ErrorBag\Error\FieldNotPresentError', $errors[0]);
        $this->assertEquals('The field First Name is required.', (string) $errors[0]);

        $post['akID'][1]['value'] = 'Oh hai!';

        $request = new \Symfony\Component\HttpFoundation\Request(array(), $post);
        $response = $validator->validateSaveValueRequest($controller, $request);
        $error = $response->getErrorObject();
        $this->assertTrue($response->isValid());
        $this->assertFalse($error->has());
    }

    public function testEmailAddressAttribute()
    {
        // Testing a missing attribute and an attribute with an error.
        $key_type = new \Concrete\Core\Entity\Attribute\Key\Type\TextType();
        $key = new \Concrete\Core\Entity\Attribute\Key\UserKey();
        $key->setAttributeKeyID(1);
        $key->setAttributeKeyName('Email Address');
        $key->setAttributeKeyType($key_type);
        $controller = \Core::make('\Concrete\Attribute\Email\Controller');
        $controller->setAttributeKey($key);

        $validator = $controller->getValidator();
        $this->assertInstanceOf('\Concrete\Core\Attribute\StandardValidator', $validator);

        $post['akID'][1]['value'] = '';
        $request = new \Symfony\Component\HttpFoundation\Request(array(), $post);

        $response = $validator->validateSaveValueRequest($controller, $request);
        $error = $response->getErrorObject();
        $this->assertFalse($response->isValid());
        $this->assertTrue($error->has());
        $errors = $error->getList();
        $this->assertInstanceOf('\Concrete\Core\Error\ErrorBag\Error\FieldNotPresentError', $errors[0]);
        $this->assertEquals('The field Email Address is required.', (string) $errors[0]);
        $this->assertEquals(1, count($errors));

        $post['akID'][1]['value'] = 'foo';
        $request = new \Symfony\Component\HttpFoundation\Request(array(), $post);

        $response = $validator->validateSaveValueRequest($controller, $request);
        $error = $response->getErrorObject();
        $this->assertFalse($response->isValid());
        $this->assertTrue($error->has());
        $errors = $error->getList();
        $this->assertInstanceOf('\Concrete\Core\Error\ErrorBag\Error\Error', $errors[0]);
        $this->assertEquals('Invalid email address.', (string) $errors[0]);
        $this->assertEquals(1, count($errors));

        $post['akID'][1]['value'] = 'test@test.com';
        $request = new \Symfony\Component\HttpFoundation\Request(array(), $post);

        $response = $validator->validateSaveValueRequest($controller, $request);
        $error = $response->getErrorObject();
        $this->assertTrue($response->isValid());
        $this->assertFalse($error->has());
    }

    public function testAddress()
    {
        $key_type = new \Concrete\Core\Entity\Attribute\Key\Type\AddressType();
        $key = new \Concrete\Core\Entity\Attribute\Key\UserKey();
        $key->setAttributeKeyID(1);
        $key->setAttributeKeyName('Contact Address');
        $key->setAttributeKeyType($key_type);
        $controller = \Core::make('\Concrete\Attribute\Address\Controller');
        $controller->setAttributeKey($key);
        $validator = $controller->getValidator();

        $request = new \Symfony\Component\HttpFoundation\Request();
        $response = $validator->validateSaveValueRequest($controller, $request);
        $error = $response->getErrorObject();
        $this->assertFalse($response->isValid());
        $this->assertTrue($error->has());
        $errors = $error->getList();
        $this->assertInstanceOf('\Concrete\Core\Error\ErrorBag\Error\FieldNotPresentError', $errors[0]);
        $this->assertEquals('The field Contact Address is required.', (string) $errors[0]);
        $this->assertEquals(1, count($errors));

        $post['akID'][1]['address1'] = '123 SW Test';
        $post['akID'][1]['city'] = 'Portland';
        $post['akID'][1]['state_province'] = 'OR';
        $post['akID'][1]['country'] = 'US';
        $post['akID'][1]['postal_code'] = '11111';
        $request = new \Symfony\Component\HttpFoundation\Request(array(), $post);

        $response = $validator->validateSaveValueRequest($controller, $request);
        $error = $response->getErrorObject();
        $this->assertTrue($response->isValid());
        $this->assertFalse($error->has());
    }

    public function testBoolean()
    {
        $key_type = new \Concrete\Core\Entity\Attribute\Key\Type\BooleanType();
        $key = new \Concrete\Core\Entity\Attribute\Key\UserKey();
        $key->setAttributeKeyID(1);
        $key->setAttributeKeyName('Is Featured');
        $key->setAttributeKeyType($key_type);
        $controller = \Core::make('\Concrete\Attribute\Boolean\Controller');
        $controller->setAttributeKey($key);
        $validator = $controller->getValidator();

        $request = new \Symfony\Component\HttpFoundation\Request();
        $response = $validator->validateSaveValueRequest($controller, $request);
        $error = $response->getErrorObject();
        $this->assertFalse($response->isValid());
        $this->assertTrue($error->has());
        $errors = $error->getList();
        $this->assertInstanceOf('\Concrete\Core\Error\ErrorBag\Error\FieldNotPresentError', $errors[0]);
        $this->assertEquals('The field Is Featured is required.', (string) $errors[0]);
        $this->assertEquals(1, count($errors));

        $post['akID'][1]['value'] = '1';
        $request = new \Symfony\Component\HttpFoundation\Request(array(), $post);

        $response = $validator->validateSaveValueRequest($controller, $request);
        $error = $response->getErrorObject();
        $this->assertTrue($response->isValid());
        $this->assertFalse($error->has());
    }

    public function testDateTime()
    {
        $key_type = new \Concrete\Core\Entity\Attribute\Key\Type\BooleanType();
        $key = new \Concrete\Core\Entity\Attribute\Key\UserKey();
        $key->setAttributeKeyID(1);
        $key->setAttributeKeyName('Is Featured');
        $key->setAttributeKeyType($key_type);
        $controller = \Core::make('\Concrete\Attribute\Boolean\Controller');
        $controller->setAttributeKey($key);
        $validator = $controller->getValidator();

        $request = new \Symfony\Component\HttpFoundation\Request();
        $response = $validator->validateSaveValueRequest($controller, $request);
        $error = $response->getErrorObject();
        $this->assertFalse($response->isValid());
        $this->assertTrue($error->has());
        $errors = $error->getList();
        $this->assertInstanceOf('\Concrete\Core\Error\ErrorBag\Error\FieldNotPresentError', $errors[0]);
        $this->assertEquals('The field Is Featured is required.', (string) $errors[0]);
        $this->assertEquals(1, count($errors));

        $post['akID'][1]['value'] = '1';
        $request = new \Symfony\Component\HttpFoundation\Request(array(), $post);

        $response = $validator->validateSaveValueRequest($controller, $request);
        $error = $response->getErrorObject();
        $this->assertTrue($response->isValid());
        $this->assertFalse($error->has());
    }


}
