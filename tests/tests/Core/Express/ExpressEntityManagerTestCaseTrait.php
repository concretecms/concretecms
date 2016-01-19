<?php

trait ExpressEntityManagerTestCaseTrait
{
    protected function getAttributeKeyObject()
    {
        $type = new \Concrete\Core\Entity\Attribute\Type();
        $type->setAttributeTypeHandle('text');
        $key_type = new \Concrete\Core\Entity\Attribute\Key\Type\TextType();
        $key_type->setAttributeType($type);

        $first_name = new Concrete\Core\Entity\Attribute\Key\Key();
        $first_name->setAttributeKeyHandle('first_name');
        $first_name->setAttributeKeyType($key_type);
        return $first_name;
    }

    protected function getMockEntityManager()
    {
        $student = new \Concrete\Core\Entity\Express\Entity();
        $student->setTableName('Students');
        $student->setName('Student');

        $teacher = new \Concrete\Core\Entity\Express\Entity();
        $teacher->setTableName('Teachers');
        $teacher->setName('Teacher');

        $attribute = new \Concrete\Core\Entity\Express\Attribute();
        $attribute->setAttributeKey($this->getAttributeKeyObject());

        $student->getAttributes()->add($attribute);

        return $this->deliverEntityManager(array($student, $teacher));
    }

    protected function deliverEntityManager($entities)
    {

        // Now, mock the repository so it returns the mock of the employee
        $entityRepository = $this
            ->getMockBuilder('\Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $entityRepository->expects($this->any())
            ->method('findAll')
            ->will($this->returnValue($entities));

        $entityRepository->expects($this->any())
            ->method('findOneBy')
            ->will($this->returnCallback(function ($args) use ($entities) {
                foreach ($entities as $entity) {
                    if (isset($args['name']) && $entity->getName() == $args['name']) {
                        return $entity;
                    } else if (isset($args['akHandle']) && $args['akHandle'] == 'first_name') {
                        return $this->getAttributeKeyObject();
                    }
                }
            }));

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

        $first_name = new Concrete\Core\Entity\Attribute\Key\Key();
        $first_name->setAttributeKeyHandle('first_name');

        $teacher = new \Concrete\Core\Entity\Express\Entity();
        $teacher->setTableName('Teachers');
        $teacher->setName('Teacher');

        $attribute = new \Concrete\Core\Entity\Express\Attribute();
        $attribute->setAttributeKey($first_name);

        $student->getAttributes()->add($attribute);

        $builder = Core::make('express.builder.association');
        $builder->addOneToMany($teacher, $student);
        $builder->addManyToOne($student, $teacher);

        return $this->deliverEntityManager(array($student, $teacher));
    }
}
