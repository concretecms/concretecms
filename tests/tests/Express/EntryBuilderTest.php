<?php

namespace Concrete\Tests\Express;

use Concrete\TestHelpers\Database\ConcreteDatabaseTestCase;
use Core;
use Express;

class EntryBuilderTest extends ConcreteDatabaseTestCase
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
        'Concrete\Core\Entity\Express\Entry\Association',
        'Concrete\Core\Entity\Express\Entry\AssociationEntry',
        'Concrete\Core\Entity\Attribute\Value\ExpressValue',
        'Concrete\Core\Entity\Attribute\Value\Value\Value',
        'Concrete\Core\Entity\Attribute\Value\Value\TextValue',
        'Concrete\Core\Entity\Attribute\Value\Value\ExpressValue',
        'Concrete\Core\Entity\Attribute\Value\Value\AddressValue',
        'Concrete\Core\Entity\Express\Association',
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

        $factory = \Core::make('\Concrete\Core\Attribute\TypeFactory');
        $factory->add('text', 'Text');
        $factory->add('address', 'Address');
        $factory->add('textarea', 'Textarea');
    }

    public function testGetEntryBuilder()
    {
        $student = $this->getStudentObjectForCRUD();

        $builder1 = Express::buildEntry('student');
        $builder2 = Express::buildEntry($student);
        $builder3 = Express::buildEntry($student->getEntity());

        $this->assertInstanceOf('Concrete\Core\Express\EntryBuilder', $builder1);
        $this->assertInstanceOf('Concrete\Core\Express\EntryBuilder', $builder2);
        $this->assertInstanceOf('Concrete\Core\Express\EntryBuilder', $builder3);
        $this->assertInstanceOf('Concrete\Core\Entity\Express\Entity', $builder1->getEntity());
        $this->assertEquals($builder1, $builder2);
        $this->assertEquals($builder2, $builder3);
    }

    public function testCreateEntry()
    {
        $this->getStudentObjectForCRUD();
        $address = new \Concrete\Core\Entity\Attribute\Value\Value\AddressValue();
        $address->setAddress1('123 SW Test');
        $address->setCity('Portland');
        $address->setStateProvince('OR');
        $address->setPostalCode('97200');

        $entry = Express::buildEntry('student')
            ->setStudentFirstName('Andrew')
            ->setStudentLastName('Embler')
            ->setStudentContactAddress($address)
            ->save();

        $this->assertInstanceOf('Concrete\Core\Entity\Express\Entry', $entry);
        $this->assertEquals(1, $entry->getID());

        $category = Core::make('Concrete\Core\Attribute\Category\ExpressCategory');
        $values = $category->getAttributeValues($entry);

        $this->assertEquals(3, count($values));

        $value = $values[0]->getDisplayValue();
        $this->assertEquals('Andrew', $value);

        $address = $entry->getStudentContactAddress();
        $this->assertInstanceOf('Concrete\Core\Entity\Attribute\Value\Value\AddressValue', $address);
        $this->assertEquals('Portland', $address->getCity());
    }

    public function testCreateEntryWithAssociation()
    {
        $student = $this->getStudentObjectForCRUD();
        $teacher = Express::getObjectByHandle('teacher');

        $builder = $student->buildAssociation();
        $builder->addManyToOne($teacher);
        $builder->save();

        $builder = Express::buildEntry('student');

        $entry1 = $builder
            ->setStudentFirstName('Andrew')
            ->setStudentLastName('Embler')
            ->save();

        $entry2 = $builder
            ->setStudentFirstName('Jane')
            ->setStudentLastName('Doe')
            ->save();

        $entry3 = $builder
            ->setStudentFirstName('Final')
            ->setStudentLastName('Student')
            ->save();

        $teacherEntry = Express::buildEntry('teacher')
            ->setTeacherFirstName('Albert')
            ->setTeacherLastName('Einstein')
            ->save();

        $entry3->associateEntries()
            ->setTeacher($teacherEntry);

        $entry4 = $builder
            ->setStudentFirstName('Just')
            ->setStudentLastName('Kidding')
            ->setTeacher($teacherEntry)
            ->save();

        $teacherEntry = Express::refresh($teacherEntry);
        $entry2 = Express::refresh($entry2);
        $entry3 = Express::refresh($entry3);
        $entry4 = Express::refresh($entry4);

        $students = $teacherEntry->getStudents();
        $this->assertEquals(1, count($students));

        $teacher1 = $entry2->getTeacher();
        $teacher2 = $entry3->getTeacher();
        $teacher3 = $entry4->getTeacher();

        $this->assertNull($teacher1);
        $this->assertInstanceOf('Concrete\Core\Entity\Express\Entry', $teacher2);
        $this->assertEquals('Albert', $teacher2->getTeacherFirstName());
        $this->assertEquals('Albert', $teacher3->getTeacherFirstName());

        $teacherEntry->associateEntries()
            ->setStudents([$entry1, $entry2]);

        $entry2 = Express::refresh($entry2);
        $teacher1 = $entry2->getTeacher();
        $this->assertInstanceOf('Concrete\Core\Entity\Express\Entry', $teacher1);

        $teacherEntry = Express::refresh($teacherEntry);
        $students = $teacherEntry->getStudents();
        $this->assertEquals(2, count($students));

        $entry3 = Express::refresh($entry3);
        $teacher2 = $entry3->getTeacher();
        $this->assertNull($teacher2);
    }

    public function testReadEntry()
    {
        $this->getStudentObjectForCRUD();

        Express::buildEntry('student')
            ->setStudentFirstName('Andrew')
            ->setStudentLastName('Embler')
            ->save();

        $entry = Express::getEntry(1);
        $this->assertInstanceOf('Concrete\Core\Entity\Express\Entry', $entry);
        $this->assertEquals('Andrew', $entry->getStudentFirstName());
    }

    public function testUpdateEntry()
    {
        $this->getStudentObjectForCRUD();

        Express::buildEntry('student')
            ->setStudentFirstName('Andrew')
            ->setStudentLastName('Embler')
            ->save();

        $entry = Express::getEntry(1);
        $entry->setStudentFirstName('Andy');

        $entry = Express::refresh($entry);
        $this->assertEquals('Andy', $entry->getStudentFirstName());
    }

    public function testDeleteEntry()
    {
        $this->getStudentObjectForCRUD();

        Express::buildEntry('student')
            ->setStudentFirstName('Andrew')
            ->setStudentLastName('Embler')
            ->save();

        Express::buildEntry('student')
            ->setStudentFirstName('Jane')
            ->setStudentLastName('Doe')
            ->save();

        $entry1 = Express::getEntry(1);
        $entry2 = Express::getEntry(2);
        $this->assertEquals('Andrew', $entry1->getStudentFirstName());
        $this->assertEquals('Jane', $entry2->getStudentFirstName());

        Express::deleteEntry($entry1);

        $entry1 = Express::getEntry(1);
        $entry2 = Express::getEntry(2);

        $this->assertNull($entry1);
        $this->assertEquals('Jane', $entry2->getStudentFirstName());
    }

    protected function getStudentObjectForCRUD()
    {
        $student = Express::buildObject('student', 'students', 'Student');
        $student->addAttribute('text', 'First Name', 'student_first_name');
        $student->addAttribute('text', 'Last Name', 'student_last_name');
        $student->addAttribute('textarea', 'Bio', 'student_bio');
        $student->addAttribute('address', 'Address', 'student_contact_address');
        $student->save();

        $teacher = Express::buildObject('teacher', 'teachers', 'Teacher');
        $teacher->addAttribute('text', 'First Name', 'teacher_first_name');
        $teacher->addAttribute('text', 'Last Name', 'teacher_last_name');
        $teacher->save();

        return $student;
    }
}
