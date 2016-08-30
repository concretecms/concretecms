<?php

class AttributeKeyHandleGeneratorTest extends ConcreteDatabaseTestCase
{

    protected $metadatas = [
        'Concrete\Core\Entity\Express\Entity',
        'Concrete\Core\Entity\Attribute\Key\Key',
        'Concrete\Core\Entity\Attribute\Key\ExpressKey',
    ];

    public function testHandle()
    {
        $entity = new \Concrete\Core\Entity\Express\Entity();
        $entity->setName('Teacher');
        $key = new \Concrete\Core\Entity\Attribute\Key\ExpressKey();
        $key->setAttributeKeyName('First Name');
        $key->setEntity($entity);

        $category = $entity->getAttributeKeyCategory();
        $generator = new \Concrete\Core\Express\Attribute\AttributeKeyHandleGenerator($category);

        $handle = $generator->generate($key);
        $this->assertEquals('teacher_first_name', $handle);

        $entity = new \Concrete\Core\Entity\Express\Entity();
        $entity->setHandle('crappy_generated_handle');
        $entity->setName('Featured Author');
        $entity->setEntityResultsNodeId(0);

        $key = new \Concrete\Core\Entity\Attribute\Key\ExpressKey();
        $key->setAttributeKeyName('Last Name!');
        $key->setEntity($entity);

        $handle = $generator->generate($key);
        $this->assertEquals('featured_author_last_name', $handle);
        $key->setAttributeKeyHandle($handle);

        $em = \Database::connection()->getEntityManager();
        $em->persist($entity);
        $em->persist($key);
        $em->flush();

        $key = new \Concrete\Core\Entity\Attribute\Key\ExpressKey();
        $key->setAttributeKeyName('Last Name.');
        $key->setEntity($entity);
        $handle = $generator->generate($key);
        $this->assertEquals('featured_author_last_name_2', $handle);
        $key->setAttributeKeyHandle($handle);

        $em = \Database::connection()->getEntityManager();
        $em->persist($key);
        $em->flush();

        $key = new \Concrete\Core\Entity\Attribute\Key\ExpressKey();
        $key->setAttributeKeyName('Final Last Name.');
        $key->setEntity($entity);
        $handle = $generator->generate($key);
        $this->assertEquals('featured_author_final_last_name', $handle);
        $key->setAttributeKeyHandle($handle);

        $em = \Database::connection()->getEntityManager();
        $em->persist($key);
        $em->flush();

        $key = new \Concrete\Core\Entity\Attribute\Key\ExpressKey();
        $key->setAttributeKeyName('.Last Name');
        $key->setEntity($entity);
        $handle = $generator->generate($key);
        $this->assertEquals('featured_author_last_name_3', $handle);
    }
}
