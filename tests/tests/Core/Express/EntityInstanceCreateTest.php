<?php


require_once __DIR__ . "/ObjectBuilderTestTrait.php";

class EntityInstanceCreateTest extends ConcreteDatabaseTestCase
{
    /*
    use \ObjectBuilderTestTrait;

    protected $tables = array(
        'AttributeTypes',
        'atTextareaSettings'
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
            ->will($this->returnCallback(function($args) use ($entityRepository) {
                if ($args == '\Concrete\Express\Teacher') {
                    return $entityRepository;
                }
            }));


        return $entityManager;
    }
    */

    public function testCreateFromRequest()
    {
    }
}
