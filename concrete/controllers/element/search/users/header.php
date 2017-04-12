<?php
namespace Concrete\Controller\Element\Search\Users;

use Concrete\Core\Controller\ElementController;
use Concrete\Core\Entity\Search\Query;
use Core;

class Header extends ElementController
{
    protected $query;
    protected $showAddButton = false;

    /**
     * @return bool
     */
    public function isShowAddButton()
    {
        return $this->showAddButton;
    }

    /**
     * @param bool $showAddButton
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
        $this->set('canExportUsers', $this->canExportUsers());
        $this->set('showAddButton', $this->showAddButton);
        $this->set('query', $this->query);
    }

    /**
     * @return bool
     */
    private function canExportUsers()
    {
        $dh = Core::make('helper/concrete/user');

        return $dh->canAccessUserSearchInterface();
    }
}
