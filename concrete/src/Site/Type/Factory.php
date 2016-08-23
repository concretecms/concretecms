<?php
namespace Concrete\Core\Site\Type;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Entity\Site\Type;
use Concrete\Core\Site\Config\Liaison;
use Doctrine\ORM\EntityManagerInterface;

class Factory
{

    public function createDefaultEntity()
    {
        $type = $this->createEntity();
        $type->setSiteTypeHandle('default');
        $type->setSiteTypeName(t('Default Site Type'));
        return $type;
    }

    public function createEntity()
    {
        $type = new Type();
        return $type;
    }



}
