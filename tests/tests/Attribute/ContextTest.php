<?php

class ContextText extends PHPUnit_Framework_TestCase
{

    public function testCreateAttributePanelContext()
    {
        $context = new \Concrete\Core\Attribute\Context\AttributePanelContext();
        $this->assertEquals(2, count($context->getActions()));
        $this->assertEquals(2, count($context->getTemplates()));
    }

    protected function getKey($controller, $key_type, $atHandle)
    {
        $entityManager = $this
            ->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $key = new \Concrete\Core\Entity\Attribute\Key\PageKey();
        $type = $this->getMockBuilder('Concrete\Core\Entity\Attribute\Type')
            ->getMock();
        $controller = new $controller($entityManager);
        $controller->setApplication(\Core::make("app"));
        $controller->setAttributeType($type);
        $type->expects($this->once())
            ->method('getController')
            ->will($this->returnValue($controller));
        $type->expects($this->any())
            ->method('getAttributeTypeHandle')
            ->will($this->returnValue($atHandle));

        $key_type = $this->getMockBuilder($key_type)
            ->getMock();
        $key_type->expects($this->any())
            ->method('getAttributeType')
            ->will($this->returnValue($type));

        $key->setAttributeKeyType($key_type);
        return $key;
    }

    public function testAttributeTextComposerForm()
    {

        $key = $this->getKey(
            'Concrete\Attribute\Text\Controller',
            'Concrete\Core\Entity\Attribute\Key\Type\TextType',
            'text'
        );
        $av = new \Concrete\Core\Attribute\View($key);
        ob_start();
        $av->render(new \Concrete\Core\Attribute\Context\ComposerContext());
        ob_end_clean();

        $this->assertEquals('composer', $av->controller->getAction());
        // form, because it falls back.
        $this->assertEquals(DIR_BASE_CORE . '/attributes/text/form.php', $av->getViewTemplate());

    }

    public function testAttributeAddressComposerForm()
    {

        $key = $this->getKey(
            'Concrete\Attribute\Address\Controller',
            'Concrete\Core\Entity\Attribute\Key\Type\AddressType',
            'address'
        );
        $av = new \Concrete\Core\Attribute\View($key);
        ob_start();
        $av->render(new \Concrete\Core\Attribute\Context\ComposerContext());
        ob_end_clean();

        $this->assertEquals('form', $av->controller->getAction());
        $this->assertEquals(DIR_BASE_CORE . '/attributes/address/composer.php', $av->getViewTemplate());
    }


}
