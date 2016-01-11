<?php


require_once __DIR__ . "/ObjectBuilderTestTrait.php";

class FormRendererTest extends ConcreteDatabaseTestCase
{
    use \ObjectBuilderTestTrait;

    protected $tables = array(
        'AttributeTypes',
        'atTextareaSettings',
        'Config',
    );

    protected function getMockEntityManager()
    {
        $entityRepository = $this
            ->getMockBuilder('\Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $entityRepository->expects($this->any())
            ->method('findAll')
            ->will($this->returnValue(array()));

        // Last, mock the EntityManager to return the mock of the repository
        $entityManager = $this
            ->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();
        $entityManager->expects($this->any())
            ->method('getRepository')
            ->will($this->returnCallback(function ($args) use ($entityRepository) {
                if ($args == '\Concrete\Express\Teacher') {
                    return $entityRepository;
                }
            }));

        return $entityManager;
    }

    protected function getForm()
    {
        \Concrete\Core\Attribute\Type::add('text', 'Text');
        \Concrete\Core\Attribute\Type::add('textarea', 'textarea');

        $builder = $this->getObjectBuilder();
        /*
         * @var \Concrete\Core\Entity\Express\Entity
         */
        $student = $builder->buildObject();
        $form = new \Concrete\Core\Entity\Express\Form();

        // create field sets
        $fieldSet1 = new \Concrete\Core\Entity\Express\FieldSet();
        $fieldSet2 = new \Concrete\Core\Entity\Express\FieldSet();

        // Create our form controls
        $name = new \Concrete\Core\Entity\Express\Control\EntityNameControl();
        $explanation = new \Concrete\Core\Entity\Express\Control\TextControl();
        $explanation->setText('This is an explanation');

        // Create association
        $teacher = new \Concrete\Core\Entity\Express\Entity();
        $teacher->setName('Teacher');
        $associationBuilder = Core::make('express.builder.association');
        $associationBuilder->addOneToMany($student, $teacher);

        $studentAssociations = $student->getAssociations();
        $associationControl = new \Concrete\Core\Entity\Express\Control\AssociationControl();
        $associationControl->setAssociation($studentAssociations[0]);

        $fieldSet1->getControls()->add($name);
        $fieldSet1->getControls()->add($explanation);
        $fieldSet1->getControls()->add($associationControl);

        // Attribute controls
        foreach ($student->getAttributes() as $attribute) {
            $attributeControl = new \Concrete\Core\Entity\Express\Control\AttributeKeyControl();
            $attributeControl->setAttributeKey($attribute);
            $fieldSet2->getControls()->add($attributeControl);
        }

        // Add field set to form
        $form->getFieldSets()->add($fieldSet1);
        $form->getFieldSets()->add($fieldSet2);
        $form->setEntity($student);

        return $form;
    }

    public function testRenderForm()
    {
        $form = $this->getForm();
        // Render the form
        //$environment = $this->getMock('\Concrete\Core\Foundation\Environment');
        $renderer = Core::make('Concrete\Core\Express\Form\Renderer', array(Core::make('app'), $this->getMockEntityManager()));
        $html = $renderer->render($form);

        preg_match_all('/ccm_token/', $html, $matches);
        $this->assertEquals(1, count($matches[0]));

        preg_match_all('/ccm_express\[name\]/', $html, $matches);
        $this->assertEquals(3, count($matches[0]));
        preg_match_all('/class="ccm-express-form-field-set"/', $html, $matches);
        $this->assertEquals(2, count($matches[0]));
        preg_match_all('/<fieldset/', $html, $matches);
        $this->assertEquals(2, count($matches[0]));

        preg_match_all('/<input type="text"/', $html, $matches);
        $this->assertEquals(4, count($matches[0]));

        preg_match_all('/<textarea/', $html, $matches);
        $this->assertEquals(1, count($matches[0]));
    }
}
