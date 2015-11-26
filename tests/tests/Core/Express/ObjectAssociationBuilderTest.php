<?php
use Concrete\Core\Express\ObjectBuilder;

require __DIR__ . '/ExpressEntityManagerTestCaseTrait.php';

class ObjectAssociationBuilderTest extends PHPUnit_Framework_TestCase
{

    use \ExpressEntityManagerTestCaseTrait;

    public function testCreateDataObject()
    {

        $em = $this->getMockEntityManager();

        /**
         * @var $builder \Concrete\Core\Express\ObjectBuilder;
         */
        $builder = Core::make('express.builder.association');
        $builder->setEntityManager($em);
        $builder->addManyToOne('Student', 'Teacher');
        // many to one with "teacher" as the field
        $relation1 = $builder->buildRelation();

        $builder->addManyToOne('Student', 'Teacher');
        $builder->addOneToMany('Teacher', 'Student');
        // many to one with "student" and "teacher" as the fields
        $relation2 = $builder->buildRelation();

        $builder->addOneToOne('Cart', 'CartProductList');
        // one to one uni with cart_product_list as the field
        $relation3 = $builder->buildRelation();

        // one to one uni with cart and customer as the fields
        $builder->addOneToOne('Cart', 'Customer');
        $builder->addOneToOne('Customer', 'Cart');
        $relation4 = $builder->buildRelation();

        $builder->addOneToOne('Student', 'Student', 'mentor');
        // one to one with mentor as the self referencing field.
        $relation5 = $builder->buildRelation();


    }


}
