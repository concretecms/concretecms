<?php
namespace Concrete\Controller\Element\Search\Files;

use Concrete\Core\Controller\ElementController;
use Concrete\Core\Entity\Search\Query;
use Concrete\Core\Search\ProviderInterface;

class Header extends ElementController
{

    protected $query;

    public function __construct(Query $query = null)
    {
        $this->query = $query;
        parent::__construct();
    }

    public function getElement()
    {
        return 'files/search_header';
    }

    public function view()
    {
        $this->set('currentFolder', 0);
        $this->set('addFolderAction', \URL::to('/ccm/system/file/folder/add'));
        $this->set('query', $this->query);
        $this->set('form', \Core::make('helper/form'));
        $this->set('token', \Core::make('token'));
    }

}
