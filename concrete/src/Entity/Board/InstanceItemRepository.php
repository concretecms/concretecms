<?php
namespace Concrete\Core\Entity\Board;

use Concrete\Core\Entity\Board\DataSource\ConfiguredDataSource;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;

class InstanceItemRepository extends EntityRepository
{

    protected function getDataSourceQueryBuilder(ConfiguredDataSource $configuredDataSource, Instance $instance)
    {
        return $this->createQueryBuilder('instanceItem')
            ->leftJoin('instanceItem.item', 'instanceItemItem')
            ->where('instanceItem.instance = :instance')
            ->andWhere('instanceItem.data_source = :configuredDataSource')
            ->setParameter('instance', $instance)
            ->setParameter('configuredDataSource', $configuredDataSource);
    }

    public function getItemCount(ConfiguredDataSource $configuredDataSource, Instance $instance)
    {
        return $this->getDataSourceQueryBuilder($configuredDataSource, $instance)
                ->select('count(instanceItem)')
                ->getQuery()
                ->getSingleScalarResult();
    }

    public function findByDataSource(
        ConfiguredDataSource $configuredDataSource,
        Instance $instance,
        string $keywords = null
    )
    {
        $qb = $this->getDataSourceQueryBuilder($configuredDataSource, $instance)
            ->select('instanceItem');
        if ($keywords) {
            $qb->andWhere($qb->expr()->like('instanceItemItem.name', ':keywords'));
            $qb->setParameter('keywords', '%' . $keywords . '%');
        }
        return $qb
            ->getQuery()
            ->execute();
    }

}
