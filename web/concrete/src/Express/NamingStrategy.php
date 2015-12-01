<?php
namespace Concrete\Core\Express;



use Concrete\Core\Application\Application;
use Doctrine\ORM\EntityManagerInterface;

class NamingStrategy implements \Doctrine\ORM\Mapping\NamingStrategy
{

    protected $rootEntityManager;


    protected $table_prefix;

    public function __construct(EntityManagerInterface $rootEntityManager)
    {
        $this->rootEntityManager = $rootEntityManager;
    }

    /**
     * @return mixed
     */
    public function getTablePrefix()
    {
        return $this->table_prefix;
    }

    /**
     * @param mixed $table_prefix
     */
    public function setTablePrefix($table_prefix)
    {
        $this->table_prefix = $table_prefix;
    }

    public function referenceColumnName()
    {
        return 'id';
    }

    public function classToTableName($className)
    {
        if (strpos($className, '\\') !== false) {
            $name = substr($className, strrpos($className, '\\') + 1);
        } else {
            $name = $className;
        }

        $repository = $this->rootEntityManager->getRepository('Concrete\Core\Entity\Express\Entity');
        $entity = $repository->findOneBy(array('name' => $name));
        return $entity->getTableName();
    }

    public function propertyToColumnName($propertyName, $className = null)
    {
        return $propertyName;
    }
    public function joinColumnName($propertyName, $className = null)
    {
        return lcfirst(camelcase($propertyName)) . camelcase($this->referenceColumnName());
    }
    public function joinTableName($sourceEntity, $targetEntity, $propertyName = null)
    {
        return $this->table_prefix . $this->classToTableName($sourceEntity) . $this->classToTableName($targetEntity);
    }
    public function joinKeyColumnName($entityName, $referencedColumnName = null)
    {
        return lcfirst($this->classToTableName($entityName)) . strtoupper(($referencedColumnName ?: $this->referenceColumnName()));
    }


}
