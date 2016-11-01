<?php
namespace Concrete\Controller\Element\Dashboard\Pages\Types;

use Concrete\Core\Controller\ElementController;
use Concrete\Core\Entity\Site\Type;

class Header extends ElementController
{

    protected $type;

    public function getElement()
    {
        return 'dashboard/pages/types/header';
    }

    public function __construct(Type $type)
    {
        $this->type = $type;
        parent::__construct();
    }

    public function view()
    {
        $siteTypes = \Core::make('site/type')->getList();
        $this->set('siteTypes', $siteTypes);
        $this->set('currentSiteType', $this->type);
        if (!$this->type->isDefault()) {
            $this->set('siteTypeID', $this->type->getSiteTypeID());
        }
    }

}
