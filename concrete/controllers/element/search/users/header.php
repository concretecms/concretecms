<?php
namespace Concrete\Controller\Element\Search\Users;

use Concrete\Core\Controller\ElementController;
use Concrete\Core\Entity\Search\Query;
use Concrete\Core\Search\ProviderInterface;

class Header extends ElementController
{

    protected $query;
    protected $showAddButton = false;

    /**
     * @return boolean
     */
    public function isShowAddButton()
    {
        return $this->showAddButton;
    }

    /**
     * @param boolean $showAddButton
     */
    public function setShowAddButton($showAddButton)
    {
        $this->showAddButton = $showAddButton;
    }

    public function __construct(Query $query = null)
    {
        $this->query = $query;
        parent::__construct();
    }

    public function getElement()
    {
        return 'users/search_header';
    }

    public function view()
    {
        $this->set('showAddButton', $this->showAddButton);
        $this->set('query', $this->query);
    }

}
