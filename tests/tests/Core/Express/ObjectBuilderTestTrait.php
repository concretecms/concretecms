<?php

trait ObjectBuilderTestTrait
{

    protected function getObjectBuilder()
    {

        /**
         * @var $builder \Concrete\Core\Express\ObjectBuilder;
         */
        $builder = Core::make('express.builder.object');
        $builder->createObject('Student')
            ->addAttribute('text', 'First Name')
            ->addAttribute('text', 'Last Name');
        $builder->createAttribute('text_area', 'Bio')
            ->setMode('rich_text')
            ->setIsIndexed(true)
            ->build();
        $builder->addAttribute('text', 'Password');
        return $builder;

    }

}
