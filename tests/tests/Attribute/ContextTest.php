<?php

namespace Concrete\Tests\Attribute;

use PHPUnit_Framework_TestCase;

class ContextTest extends PHPUnit_Framework_TestCase
{
    public function testCreateAttributePanelContext()
    {
        $context = new \Concrete\Core\Attribute\Context\AttributePanelContext();
        $this->assertEquals(2, count($context->getActions()));
        $this->assertEquals(2, count($context->getControlTemplates()));
    }

    public function testAttributeTextComposerForm()
    {
        $key = $this->getKey(
            'Concrete\Attribute\Text\Controller',
            'Concrete\Core\Entity\Attribute\Key\Settings\TextSettings',
            'text'
        );
        $av = new \Concrete\Core\Attribute\View($key);
        ob_start();
        $av->render(new \Concrete\Core\Attribute\Context\ComposerContext());
        ob_end_clean();

        $this->assertEquals('form', $av->controller->getAction());
        // form, because it falls back.
        $this->assertEquals(str_replace(DIRECTORY_SEPARATOR, '/', DIR_BASE_CORE . '/attributes/text/form.php'), str_replace(DIRECTORY_SEPARATOR, '/', $av->getViewTemplate()));
    }

    public function testAttributeAddressComposerForm()
    {
        $key = $this->getKey(
            'Concrete\Attribute\Address\Controller',
            'Concrete\Core\Entity\Attribute\Key\Settings\AddressSettings',
            'address'
        );
        $av = new \Concrete\Core\Attribute\View($key);
        ob_start();
        $av->render(new \Concrete\Core\Attribute\Context\ComposerContext());
        ob_end_clean();

        $this->assertEquals('form', $av->controller->getAction());
        $this->assertEquals(str_replace(DIRECTORY_SEPARATOR, '/', DIR_BASE_CORE . '/attributes/address/form.php'), str_replace(DIRECTORY_SEPARATOR, '/', $av->getViewTemplate()));
    }

    protected function getKey($controller, $key_type, $atHandle)
    {
        $entityManager = $this
            ->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $type = $this->getMockBuilder('Concrete\Core\Entity\Attribute\Type')
            ->getMock();
        $controller = new $controller($entityManager);
        $controller->setApplication(\Core::make('app'));
        $controller->setAttributeType($type);
        $type->expects($this->once())
            ->method('getController')
            ->will($this->returnValue($controller));
        $type->expects($this->any())
            ->method('getAttributeTypeHandle')
            ->will($this->returnValue($atHandle));

        $key_type = $this->getMockBuilder($key_type)
            ->getMock();
        $key = $this->getMockBuilder('Concrete\Core\Entity\Attribute\Key\PageKey')
            ->getMock();
        $key->expects($this->any())
            ->method('getAttributeType')
            ->will($this->returnValue($type));
        $key->expects($this->any())
            ->method('getAttributeKeySettings')
            ->will($this->returnValue($key_type));

        return $key;
    }
}
