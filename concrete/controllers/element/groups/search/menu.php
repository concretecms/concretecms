<?php

namespace Concrete\Controller\Element\Groups\Search;

use Concrete\Core\Controller\ElementController;
use Concrete\Core\Entity\Search\Query;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\Type\GroupFolder;
use Concrete\Core\User\Group\Search\SearchProvider;
use Concrete\Core\Utility\Service\Url;
use Concrete\Core\Validation\CSRF\Token;

class Menu extends ElementController
{
    protected $query;
    protected $searchProvider;

    /**
     * @var Node|GroupFolder
     */
    protected $currentFolder;


    public function __construct(SearchProvider $searchProvider)
    {
        parent::__construct();
        $this->searchProvider = $searchProvider;
    }

    public function getElement()
    {
        return 'groups/search/menu';
    }

    public function setQuery(Query $query): void
    {
        $this->query = $query;
    }

    /**
     * @param Node|GroupFolder $currentFolder
     */
    public function setCurrentFolder(Node $currentFolder): void
    {
        $this->currentFolder = $currentFolder;
    }

    public function view()
    {
        $itemsPerPage = (isset($this->query)) ? $this->query->getItemsPerPage() : $this->searchProvider->getItemsPerPage();
        $this->set('itemsPerPage', $itemsPerPage);
        $this->set('itemsPerPageOptions', $this->searchProvider->getItemsPerPageOptions());
        $this->set('currentFolder', $this->currentFolder);
        $this->set('form', $this->app->make(Form::class));
        $this->set('token', $this->app->make(Token::class));
        $this->set('urlHelper', $this->app->make(Url::class));
    }

}
