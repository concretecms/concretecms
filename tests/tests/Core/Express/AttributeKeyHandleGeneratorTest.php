<?php

class AttributeKeyHandleGeneratorTest extends ConcreteDatabaseTestCase
{

    protected $metadatas = [
        'Concrete\Core\Entity\Express\Entity',
        'Concrete\Core\Entity\Attribute\Key\Key',
        'Concrete\Core\Entity\Attribute\Key\ExpressKey',
    ];

    public function testExpressHandleGenerator()
    {
        $entity = new \Concrete\Core\Entity\Express\Entity();
        $entity->setName('Teacher');
        $em = \Database::connection()->getEntityManager();
        $generator = new \Concrete\Core\Express\Generator\EntityHandleGenerator($em);

        $handle = $generator->generate($entity);
        $this->assertEquals('teacher', $handle);

    }

    public function testHandle()
    {
        $em = \Database::connection()->getEntityManager();
        $entity = new \Concrete\Core\Entity\Express\Entity();
        $entity->setName('Teacher');
        $entity->setHandle('teacher');
        $key = new \Concrete\Core\Entity\Attribute\Key\ExpressKey();
        $key->setAttributeKeyName('First Name');
        $key->setEntity($entity);

        $category = $entity->getAttributeKeyCategory();
        $generator = new \Concrete\Core\Express\Attribute\AttributeKeyHandleGenerator($category);

        $handle = $generator->generate($key);
        $this->assertEquals('first_name', $handle);

        $entity = new \Concrete\Core\Entity\Express\Entity();
        $entity->setName('Featured Author');
        $entity->setHandle((new \Concrete\Core\Express\Generator\EntityHandleGenerator($em))->generate($entity));
        $entity->setEntityResultsNodeId(0);

        $key = new \Concrete\Core\Entity\Attribute\Key\ExpressKey();
        $key->setAttributeKeyName('Last Name!');
        $key->setEntity($entity);
        $this->assertEquals('featured_author', $entity->getHandle());

        $handle = $generator->generate($key);
        $this->assertEquals('last_name', $handle);
        $key->setAttributeKeyHandle($handle);

        $em = \Database::connection()->getEntityManager();
        $em->persist($entity);
        $em->persist($key);
        $em->flush();

        $key = new \Concrete\Core\Entity\Attribute\Key\ExpressKey();
        $key->setAttributeKeyName('Last Name.');
        $key->setEntity($entity);
        $handle = $generator->generate($key);
        $this->assertEquals('last_name_2', $handle);
        $key->setAttributeKeyHandle($handle);

        $em = \Database::connection()->getEntityManager();
        $em->persist($key);
        $em->flush();

        $key = new \Concrete\Core\Entity\Attribute\Key\ExpressKey();
        $key->setAttributeKeyName('Final Last Name.');
        $key->setEntity($entity);
        $handle = $generator->generate($key);
        $this->assertEquals('final_last_name', $handle);
        $key->setAttributeKeyHandle($handle);

        $em = \Database::connection()->getEntityManager();
        $em->persist($key);
        $em->flush();

        $key = new \Concrete\Core\Entity\Attribute\Key\ExpressKey();
        $key->setAttributeKeyName('.Last Name');
        $key->setEntity($entity);
        $handle = $generator->generate($key);
        $this->assertEquals('last_name_3', $handle);
    }

    public function testLengthyAttributeKeyHandle()
    {
        $em = \Database::connection()->getEntityManager();
        $entity = new \Concrete\Core\Entity\Express\Entity();
        $entity->setName('Luxury Automobile Models');
        $entity->setHandle((new \Concrete\Core\Express\Generator\EntityHandleGenerator($em))->generate($entity));
        $key = new \Concrete\Core\Entity\Attribute\Key\ExpressKey();
        $key->setAttributeKeyName('Yes I fully and 100% and completely and inexorably agree to the terms of service');
        $key->setEntity($entity);

        $category = $entity->getAttributeKeyCategory();
        $generator = new \Concrete\Core\Express\Attribute\AttributeKeyHandleGenerator($category);

        $handle = $generator->generate($key);
        $this->assertEquals('yes_i_fully_and_100_and_completely_and_ine', $handle);

    }
}
