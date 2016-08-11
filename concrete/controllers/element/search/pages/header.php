<?php
namespace Concrete\Controller\Element\Search\Pages;

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
        return 'pages/search_header';
    }

    public function view()
    {
        $this->set('query', $this->query);
    }

}
