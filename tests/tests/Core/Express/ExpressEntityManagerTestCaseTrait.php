<?php

trait ExpressEntityManagerTestCaseTrait
{

    protected function getMockEntityManager()
    {

        $student = new \Concrete\Core\Entity\Express\Entity();
        $student->setTableName('Students');
        $student->setName('Student');

        $first_name = new Concrete\Core\Entity\AttributeKey\TextAttributeKey();
        $first_name->setHandle('first_name');

        $teacher = new \Concrete\Core\Entity\Express\Entity();
        $teacher->setTableName('Teachers');
        $teacher->setName('Teacher');

        $attribute = new \Concrete\Core\Entity\Express\Attribute();
        $attribute->setAttribute($first_name);

        $student->getAttributes()->add($attribute);

        return $this->deliverTeachersAndStudents($student, $teacher);
    }

    protected function deliverTeachersAndStudents($student, $teacher)
    {

        // Now, mock the repository so it returns the mock of the employee
        $entityRepository = $this
            ->getMockBuilder('\Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $entityRepository->expects($this->any())
            ->method('findAll')
            ->will($this->returnValue(array($student, $teacher)));

        // Last, mock the EntityManager to return the mock of the repository
        $entityManager = $this
            ->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();
        $entityManager->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($entityRepository));

        return $entityManager;
    }

    protected function getMockEntityManagerWithRelations()
    {

        $student = new \Concrete\Core\Entity\Express\Entity();
        $student->setTableName('Students');
        $student->setName('Student');

        $first_name = new Concrete\Core\Entity\AttributeKey\TextAttributeKey();
        $first_name->setHandle('first_name');


        $teacher = new \Concrete\Core\Entity\Express\Entity();
        $teacher->setTableName('Teachers');
        $teacher->setName('Teacher');

        $attribute = new \Concrete\Core\Entity\Express\Attribute();
        $attribute->setAttribute($first_name);

        $student->getAttributes()->add($attribute);

        $builder = Core::make('express.builder.association');
        $builder->addOneToMany($teacher, $student);
        $builder->addManyToOne($student, $teacher);

        return $this->deliverTeachersAndStudents($student, $teacher);
    }

}
