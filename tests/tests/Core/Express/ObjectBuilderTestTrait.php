<?php

trait ObjectBuilderTestTrait
{
    protected function getObjectBuilder()
    {

        $factory = $this
            ->getMockBuilder('\Concrete\Core\Attribute\TypeFactory')
            ->disableOriginalConstructor()
            ->getMock();


        $factory->expects($this->any())
            ->method('getByHandle')
            ->will($this->returnCallback(function ($args) {
                if ($args == 'text') {
                    $type = new \Concrete\Core\Entity\Attribute\Type();
                    $type->setAttributeTypeHandle('text');
                    return $type;
                }
                if ($args == 'textarea') {
                    $type = new \Concrete\Core\Entity\Attribute\Type();
                    $type->setAttributeTypeHandle('textarea');
                    return $type;
                }
            }));

        /*
         * @var $builder \Concrete\Core\Express\ObjectBuilder;
         */
        $builder = new Concrete\Core\Express\ObjectBuilder($factory);
        $builder->createObject('Student')
            ->addAttribute('text', 'First Name')
            ->addAttribute('text', 'Last Name');
        $builder->createAttribute('textarea', 'Bio')
            ->setMode('text')
            ->setIsAttributeKeyContentIndexed(true)
            ->build();
        $builder->addAttribute('text', 'Password');

        return $builder;
    }
}
