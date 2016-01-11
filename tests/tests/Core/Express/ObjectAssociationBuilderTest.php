<?php


require __DIR__ . '/ExpressEntityManagerTestCaseTrait.php';

class ObjectAssociationBuilderTest extends PHPUnit_Framework_TestCase
{
    use \ExpressEntityManagerTestCaseTrait;
    protected $builder;
    protected $entities;

    protected function setUp()
    {
        parent::setUp();
        $this->builder = Core::make('express.builder.association');
    }
    protected function getData()
    {
        if (!isset($this->entities)) {
            $em = $this->getMockEntityManager();
            $this->entities = $em->getRepository('Concrete\Core\Entity\Express\Entity')->findAll();
        }

        return $this->entities;
    }

    public function testManyToOneUni()
    {
        $this->builder->addManyToOne($this->getData()[0], $this->getData()[1]);
        $studentAssociations = $this->getData()[0]->getAssociations();
        $teacherAssociations = $this->getData()[1]->getAssociations();
        $this->assertEquals(1, count($studentAssociations));
        $this->assertEquals(0, count($teacherAssociations));
        $studentAssociation = $studentAssociations[0];
        $this->assertInstanceOf('\Concrete\Core\Entity\Express\ManyToOneAssociation', $studentAssociation);
        $this->assertEquals('Student', $studentAssociation->getSourceEntity()->getName());
        $this->assertEquals('Teacher', $studentAssociation->getTargetEntity()->getName());
        $this->assertNull($studentAssociation->getTargetPropertyName());
        $this->assertEquals('teacher', $studentAssociation->getComputedTargetPropertyName());
    }

    public function testOneToManyBi()
    {
        $this->builder->addManyToOne($this->getData()[0], $this->getData()[1]);
        $this->builder->addOneToMany($this->getData()[1], $this->getData()[0]);
        $studentAssociations = $this->getData()[0]->getAssociations();
        $teacherAssociations = $this->getData()[1]->getAssociations();
        $this->assertEquals(1, count($studentAssociations));
        $this->assertEquals(1, count($teacherAssociations));
        $studentAssociation = $studentAssociations[0];
        $teacherAssociation = $teacherAssociations[0];
        $this->assertInstanceOf('\Concrete\Core\Entity\Express\ManyToOneAssociation', $studentAssociation);
        $this->assertEquals('Student', $studentAssociation->getSourceEntity()->getName());
        $this->assertEquals('Teacher', $studentAssociation->getTargetEntity()->getName());
        $this->assertEquals('teacher', $studentAssociation->getComputedTargetPropertyName());
        $this->assertNull($studentAssociation->getTargetPropertyName());

        $this->assertInstanceOf('\Concrete\Core\Entity\Express\OneToManyAssociation', $teacherAssociation);
        $this->assertEquals('Teacher', $teacherAssociation->getSourceEntity()->getName());
        $this->assertEquals('Student', $teacherAssociation->getTargetEntity()->getName());
        $this->assertEquals('student', $teacherAssociation->getComputedTargetPropertyName());
        $this->assertNull($teacherAssociation->getTargetPropertyName());
    }

    public function testOneToOneUni()
    {
        $this->builder->addOneToOneUnidirectional($this->getData()[0], $this->getData()[1]);
        $studentAssociations = $this->getData()[0]->getAssociations();
        $teacherAssociations = $this->getData()[1]->getAssociations();
        $this->assertEquals(1, count($studentAssociations));
        $this->assertEquals(0, count($teacherAssociations));
        $studentAssociation = $studentAssociations[0];
        $this->assertInstanceOf('\Concrete\Core\Entity\Express\OneToOneAssociation', $studentAssociation);
        $this->assertEquals('Student', $studentAssociation->getSourceEntity()->getName());
        $this->assertEquals('Teacher', $studentAssociation->getTargetEntity()->getName());
        $this->assertEquals('teacher', $studentAssociation->getComputedTargetPropertyName());
        $this->assertNull($studentAssociation->getTargetPropertyName());
    }

    public function testOneToOneBi()
    {
        $this->builder->addOneToOne($this->getData()[0], $this->getData()[1]);
        $studentAssociations = $this->getData()[0]->getAssociations();
        $teacherAssociations = $this->getData()[1]->getAssociations();
        $this->assertEquals(1, count($studentAssociations));
        $this->assertEquals(1, count($teacherAssociations));
        $studentAssociation = $studentAssociations[0];
        $teacherAssociation = $teacherAssociations[0];
        $this->assertInstanceOf('\Concrete\Core\Entity\Express\OneToOneAssociation', $studentAssociation);
        $this->assertEquals('Student', $studentAssociation->getSourceEntity()->getName());
        $this->assertEquals('Teacher', $studentAssociation->getTargetEntity()->getName());
        $this->assertEquals('teacher', $studentAssociation->getComputedTargetPropertyName());
        $this->assertNull($studentAssociation->getTargetPropertyName());

        $this->assertInstanceOf('\Concrete\Core\Entity\Express\OneToOneAssociation', $teacherAssociation);
        $this->assertEquals('Teacher', $teacherAssociation->getSourceEntity()->getName());
        $this->assertEquals('Student', $teacherAssociation->getTargetEntity()->getName());
        $this->assertEquals('student', $teacherAssociation->getComputedTargetPropertyName());
        $this->assertNull($teacherAssociation->getTargetPropertyName());
    }

    public function testOneToOneSelf()
    {
        $this->builder->addOneToOneUnidirectional($this->getData()[0], $this->getData()[0], 'mentor');
        $studentAssociations = $this->getData()[0]->getAssociations();
        $this->assertEquals(1, count($studentAssociations));
        $studentAssociation = $studentAssociations[0];
        $this->assertInstanceOf('\Concrete\Core\Entity\Express\OneToOneAssociation', $studentAssociation);
        $this->assertEquals('Student', $studentAssociation->getSourceEntity()->getName());
        $this->assertEquals('Student', $studentAssociation->getTargetEntity()->getName());
        $this->assertEquals('mentor', $studentAssociation->getComputedTargetPropertyName());
        $this->assertEquals('mentor', $studentAssociation->getTargetPropertyName());
    }
}
