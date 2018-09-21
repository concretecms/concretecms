<?php

namespace Concrete\Tests\Express;

use Concrete\TestHelpers\Database\ConcreteDatabaseTestCase;
use Core;
use Express;

class ObjectBuilderTest extends ConcreteDatabaseTestCase
{
    protected $pkg;

    protected $tables = [
        'Trees',
        'TreeNodes',
        'TreeGroupNodes',
        'TreeTypes',
        'TreeNodeTypes',
        'TreeNodePermissionAssignments',
        'PermissionAccessEntities',
        'PermissionAccessEntityGroups',
        'PermissionAccessEntityTypes',
        'PermissionKeys',
        'PermissionKeyCategories',
        'Groups',
    ];

    protected $metadatas = [
        'Concrete\Core\Entity\Express\Entity',
        'Concrete\Core\Entity\Express\Entry',
        'Concrete\Core\Entity\Express\Association',
        'Concrete\Core\Entity\Express\Form',
        'Concrete\Core\Entity\Express\FieldSet',
        'Concrete\Core\Entity\Express\Control\Control',
        'Concrete\Core\Entity\Express\Control\AttributeKeyControl',
        'Concrete\Core\Entity\Express\Control\TextControl',
        'Concrete\Core\Entity\Package',
        'Concrete\Core\Entity\Attribute\Type',
        'Concrete\Core\Entity\Attribute\Key\ExpressKey',
        'Concrete\Core\Entity\Attribute\Key\Key',
        'Concrete\Core\Entity\Attribute\Key\Settings\TextSettings',
        'Concrete\Core\Entity\Attribute\Key\Settings\AddressSettings',
        'Concrete\Core\Entity\Attribute\Key\Settings\TextareaSettings',
    ];

    protected function setUp()
    {
        parent::setUp();

        $this->truncateTables();

        \Concrete\Core\Tree\Node\NodeType::add('category');
        \Concrete\Core\Tree\Node\NodeType::add('express_entry_category');
        \Concrete\Core\Tree\TreeType::add('express_entry_results');
        \Concrete\Core\Tree\Node\NodeType::add('express_entry_results');

        $tree = \Concrete\Core\Tree\Type\ExpressEntryResults::add();

        $em = \Database::connection()->getEntityManager();
        $pkg = new \Concrete\Core\Entity\Package();
        $pkg->setPackageHandle('test');
        $pkg->setPackageVersion('1.0');
        $pkg->setPackageDescription('sigh');
        $em->persist($pkg);
        $em->flush();

        $this->pkg = $pkg;

        $factory = \Core::make('\Concrete\Core\Attribute\TypeFactory');
        $factory->add('text', 'Text');
        $factory->add('address', 'Address');
        $factory->add('textarea', 'Textarea');
    }

    public function testBasicObjectBuilder()
    {
        $marina = Express::buildObject('marina', 'marinas', 'Marina', $this->pkg);
        $this->assertInstanceOf('Concrete\Core\Express\ObjectBuilder', $marina);
        $marina->setDescription('This is my marina object.');
        $marina = $marina->save();

        $this->assertInstanceOf('Concrete\Core\Entity\Express\Entity', $marina);
        $this->assertNotEquals('', $marina->getID());
        $this->assertEquals('This is my marina object.', $marina->getDescription());
        $this->assertEquals('Marina', $marina->getName());
        $this->assertEquals('marina', $marina->getHandle());
        $this->assertEquals('marinas', $marina->getPluralHandle());
        $this->assertNotNull($marina->getEntityResultsNodeId());
        $this->assertInstanceOf('Concrete\Core\Entity\Package', $marina->getPackage());
    }

    public function testObjectBuilderWithAttributes()
    {
        $student = Express::buildObject('student', 'students', 'Student', $this->pkg);
        $student->addAttribute('text', 'First Name');
        $settings = new \Concrete\Core\Entity\Attribute\Key\Settings\TextSettings();
        $settings->setPlaceholder('Last Name');
        $student->addAttribute('text', 'Last Name', 'last_name', $settings);
        $student = $student->save();

        $category = Core::make('Concrete\Core\Attribute\Category\ExpressCategory',
            ['entity' => $student]);

        $attributes = $category->getList();
        $key1 = $attributes[0];
        $key2 = $attributes[1];
        $this->assertEquals(2, count($attributes));
        $this->assertEquals('first_name', $key1->getAttributeKeyHandle());
        $this->assertEquals('last_name', $key2->getAttributeKeyHandle());

        $type = $key1->getAttributeType();
        $this->assertInstanceOf('Concrete\Core\Entity\Attribute\Type', $type);
        $this->assertEquals('text', $type->getAttributeTypeHandle());

        $settings1 = $key1->getAttributeKeySettings();
        $settings2 = $key2->getAttributeKeySettings();
        $this->assertEquals('', $settings1->getPlaceholder());
        $this->assertEquals('Last Name', $settings2->getPlaceholder());
    }

    public function testObjectBuilderWithAttributes2()
    {
        $student = Express::buildObject('student', 'students', 'Student', $this->pkg);
        $settings = new \Concrete\Core\Entity\Attribute\Key\Settings\AddressSettings();
        $settings->setCustomCountries(['US', 'UK']);
        $settings->setHasCustomCountries(true);
        $settings->setDefaultCountry('CA');
        $student->addAttribute('address', 'Address', 'address', $settings);
        $student = $student->save();

        $attributes = $student->getAttributes();
        $key1 = $attributes[0];
        $this->assertEquals('address', $key1->getAttributeKeyHandle());
        $type = $key1->getAttributeType();
        $this->assertInstanceOf('Concrete\Core\Entity\Attribute\Type', $type);
        $this->assertEquals('address', $type->getAttributeTypeHandle());
        $settings1 = $key1->getAttributeKeySettings();
        $this->assertEquals(true, $settings1->hasCustomCountries());
        $this->assertEquals('CA', $settings1->getDefaultCountry());
        $countries = $settings1->getCustomCountries();
        $this->assertEquals(2, count($countries));
    }

    public function testCreateObjectAndForm()
    {
        $student = Express::buildObject('student', 'students', 'Student', $this->pkg);
        $student->addAttribute('text', 'First Name');
        $student->addAttribute('text', 'Last Name');
        $student->addAttribute('textarea', 'Bio');
        $student->save();

        $form = $student->buildForm('Form');
        $form->addFieldset('Basics')
            ->addAttributeKeyControl('first_name')
            ->addAttributeKeyControl('last_name')
            ->addTextControl('', 'This is just some basic explanatory text.')
            ->addAttributeKeyControl('bio');
        $form = $form->save();

        $defaultViewForm = $student->getDefaultViewForm();
        $defaultEditForm = $student->getDefaultEditForm();
        $this->assertEquals($defaultEditForm, $form);
        $this->assertEquals($defaultViewForm, $form);

        $this->assertInstanceOf('Concrete\Core\Entity\Express\Form', $form);
        $this->assertEquals('Form', $form->getName());
        $this->assertNotEquals('', $form->getID());

        $this->assertEquals(1, count($form->getFieldSets()));
        $fieldsets = $form->getFieldSets();
        $this->assertEquals('Basics', $fieldsets[0]->getTitle());

        $controls = $fieldsets[0]->getControls();

        $this->assertEquals(4, count($controls));

        $this->assertInstanceOf('Concrete\Core\Entity\Express\Control\AttributeKeyControl', $controls[0]);
        $this->assertInstanceOf('Concrete\Core\Entity\Express\Control\AttributeKeyControl', $controls[1]);
        $this->assertInstanceOf('Concrete\Core\Entity\Express\Control\TextControl', $controls[2]);
        $this->assertInstanceOf('Concrete\Core\Entity\Express\Control\AttributeKeyControl', $controls[3]);

        $this->assertEquals('', $controls[2]->getHeadline());
        $this->assertEquals('This is just some basic explanatory text.', $controls[2]->getBody());

        $key = $controls[3]->getAttributeKey();
        $this->assertEquals('Bio', $key->getAttributeKeyName());

        $type = $key->getAttributeType();
        $this->assertInstanceOf('Concrete\Core\Entity\Attribute\Type', $type);
        $this->assertEquals('textarea', $type->getAttributeTypeHandle());

        // Create a second form.
        $secondForm = $student->buildForm('Form');
        $secondForm->addFieldset('Second Form')
            ->addAttributeKeyControl('first_name')
            ->addAttributeKeyControl('last_name')
            ->addTextControl('', 'This is just some basic explanatory text.')
            ->addAttributeKeyControl('bio');
        $secondForm = $secondForm->save();

        $defaultViewForm = $student->getDefaultViewForm();
        $defaultEditForm = $student->getDefaultEditForm();
        $this->assertEquals($defaultEditForm, $form);
        $this->assertEquals($defaultViewForm, $form);
        $this->assertNotEquals($defaultViewForm, $secondForm);
        $this->assertNotEquals($defaultViewForm, $secondForm);
        
    }

    public function testCreateAssociation()
    {
        $student = Express::buildObject('student', 'students', 'Student', $this->pkg);
        $teacher = Express::buildObject('teacher', 'teachers', 'Teacher', $this->pkg);

        $builder = $student->buildAssociation();
        $builder->addManyToOne($teacher);
        $student = $builder->save();

        $this->assertInstanceOf('Concrete\Core\Entity\Express\Entity', $student);
        $this->assertNotEquals('', $student->getID());

        $associations = $student->getAssociations();
        $this->assertEquals(1, count($associations));

        $association = $associations[0];
        /*
         * @var $association \Concrete\Core\Entity\Express\ManyToOneAssociation
         */
        $this->assertInstanceOf('Concrete\Core\Entity\Express\ManyToOneAssociation', $association);
        $this->assertEquals('teacher', $association->getTargetPropertyName());

        $teacher = $teacher->getEntity();
        $associations = $teacher->getAssociations();
        $this->assertEquals(1, count($associations));

        $association = $associations[0];
        $this->assertInstanceOf('Concrete\Core\Entity\Express\OneToManyAssociation', $association);
        $this->assertEquals('students', $association->getTargetPropertyName());
    }

    public function testOtherAssociations()
    {
        $project = Express::buildObject('project', 'projects', 'Project');
        $skill = Express::buildObject('skill', 'skills', 'Skill');
        $developer = Express::buildObject('developer', 'developers', 'Developer');

        $project->buildAssociation()->addManyToMany($skill)->save();
        $skill->buildAssociation()->addOneToOne($developer, 'best_developer')->save();

        $project = $project->getEntity();
        $skill = $skill->getEntity();
        $developer = $developer->getEntity();

        $associations = $project->getAssociations();
        $this->assertEquals(1, count($associations));
        $association = $associations[0];
        $this->assertNotEquals('', $association->getID());
        $this->assertInstanceOf('Concrete\Core\Entity\Express\ManyToManyAssociation', $association);
        $this->assertEquals('skills', $association->getTargetPropertyName());

        $associations = $skill->getAssociations();
        $this->assertEquals(2, count($associations));
        $association1 = $associations[0];
        $association2 = $associations[1];
        $this->assertInstanceOf('Concrete\Core\Entity\Express\ManyToManyAssociation', $association1);
        $this->assertNotEquals('', $association1->getID());
        $this->assertEquals('projects', $association1->getTargetPropertyName());

        $this->assertInstanceOf('Concrete\Core\Entity\Express\OneToOneAssociation', $association2);
        $this->assertNotEquals('', $association2->getID());
        $this->assertEquals('best_developer', $association2->getTargetPropertyName());
        $this->assertEquals(\Concrete\Core\Entity\Express\OneToOneAssociation::TYPE_OWNING, $association2->getAssociationType());

        $associations = $developer->getAssociations();
        $this->assertEquals(1, count($associations));
        $association = $associations[0];
        $this->assertNotEquals('', $association->getID());
        $this->assertInstanceOf('Concrete\Core\Entity\Express\OneToOneAssociation', $association);
        $this->assertEquals('skill', $association->getTargetPropertyName());
        $this->assertEquals(\Concrete\Core\Entity\Express\OneToOneAssociation::TYPE_INVERSE, $association->getAssociationType());
    }
}
