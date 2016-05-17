<?php
namespace Concrete\Controller\Element\Package;

use Concrete\Core\Controller\ElementController;
use Concrete\Core\Entity\Package;
use Concrete\Core\Package\ItemCategory\ItemInterface;

class ItemList extends ElementController
{

    protected $package;
    protected $category;

    public function __construct(ItemInterface $category, Package $package)
    {
        parent::__construct();
        $this->category = $category;
        $this->package = $package;
    }

    public function getElement()
    {
        return 'package/item_list';
    }

    public function view()
    {
        $this->set('category', $this->category);
        $this->set('package', $this->package);
    }
}
