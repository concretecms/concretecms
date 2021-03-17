<?php
namespace Concrete\Tests\Form\Context\Registry;

use Concrete\Core\Entity\Attribute\Type;
use Concrete\Core\Entity\Attribute\Key\ExpressKey;
use Concrete\Core\Entity\Express\Control\AssociationControl;
use Concrete\Core\Entity\Express\Control\AttributeKeyControl;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\OneToOneAssociation;
use Concrete\Core\Express\Form\Context\FormContext;
use Concrete\Core\Express\Form\Context\ViewContext;
use Concrete\Core\Express\Form\Control\View\AssociationFormView;
use Concrete\Core\Express\Form\Control\View\AssociationView;
use Concrete\Core\Express\Form\Control\View\AttributeKeyFormView;
use Concrete\Core\Express\Form\Control\View\AttributeKeyView;
use Concrete\Core\Form\Context\Registry\ControlRegistry;
use Concrete\Tests\Express\Form\Control\View\TestView;
use PHPUnit_Framework_TestCase;
use Symfony\Component\ClassLoader\MapClassLoader;

class ContextRegistryTest extends PHPUnit_Framework_TestCase
{
    private static $classLoader;

    public static function setUpBeforeClass()
    {
        static::$classLoader = new MapClassLoader([
            TestView::class => DIR_TESTS . '/assets/Express/Form/Control/View/TestView.php',
        ]);
        static::$classLoader->register(true);
    }

    public function testAttributeKeyControls()
    {
        $registry = new ControlRegistry();

        $akControl = $this->getAttributeKeyControl();

        $this->assertInstanceOf(
            AttributeKeyFormView::class,
            $registry->getControlView(
                new FormContext(),
                'express_control_attribute_key',
                [$akControl]
            )
        );
        $this->assertInstanceOf(
            AttributeKeyView::class,
            $registry->getControlView(
                new ViewContext(),
                'express_control_attribute_key',
                [$akControl]
            )
        );
    }

    public function testAssociationControls()
    {
        $registry = new ControlRegistry();

        $asControl = $this->getAssociationControl();

        $this->assertInstanceOf(
            AssociationFormView::class,
            $registry->getControlView(
                new FormContext(),
                'express_control_association',
                [$asControl]
            )
        );
        $this->assertInstanceOf(
            AssociationView::class,
            $registry->getControlView(
                new ViewContext(),
                'express_control_association',
                [$asControl]
            )
        );
    }

    public function testReplaceFirstControl()
    {
        $registry = new ControlRegistry();
        $registry->register(
            new FormContext(),
            'express_control_attribute_key',
            TestView::class
        );

        $this->assertInstanceOf(
            TestView::class,
            $registry->getControlView(
                new FormContext(),
                'express_control_attribute_key',
                [$this->getAttributeKeyControl()]
            )
        );
    }

    public function testReplaceLastControl()
    {
        $registry = new ControlRegistry();
        $registry->register(
            new ViewContext(),
            'express_control_association',
            TestView::class
        );

        $this->assertInstanceOf(
            TestView::class,
            $registry->getControlView(
                new ViewContext(),
                'express_control_association',
                [$this->getAssociationControl()]
            )
        );
    }

    private function getAttributeKeyControl()
    {
        $at = new Type();
        $at->setAttributeTypeHandle('text');
        $ak = new ExpressKey();
        $ak->setAttributeType($at);

        $akControl = new AttributeKeyControl();
        $akControl->setAttributeKey($ak);

        return $akControl;
    }

    private function getAssociationControl()
    {
        $entity = new Entity();
        $entity->setName('Test');

        $assoc = new OneToOneAssociation();
        $assoc->setTargetEntity($entity);

        $asControl = new AssociationControl();
        $asControl->setAssociation($assoc);
        // Use a selector mode that will not cause any database interaction
        $asControl->setEntrySelectorMode(
            AssociationControl::TYPE_ENTRY_SELECTOR
        );

        return $asControl;
    }
}
