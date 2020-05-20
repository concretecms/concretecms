<?php
namespace Concrete\Core\Entity\Board;

use Concrete\Core\Entity\Board\DataSource\ConfiguredDataSource;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;

class InstanceItemRepository extends EntityRepository
{

    public function getItemCount(ConfiguredDataSource $configuredDataSource, Instance $instance)
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('data_source', $configuredDataSource))
            ->andWhere(Criteria::expr()->eq('instance', $instance));
        return $this->matching($criteria)->count();
    }

}
