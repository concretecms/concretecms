<?php

namespace Concrete\Controller\Element\Users\Search;

use Concrete\Core\Controller\ElementController;
use Concrete\Core\Entity\Search\Query;
use Concrete\Core\User\Search\SearchProvider;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Utility\Service\Url;
use Concrete\Core\Validation\CSRF\Token;

class Menu extends ElementController
{
    protected $query;
    protected $searchProvider;
    protected $exportURL;

    public function __construct(SearchProvider $searchProvider)
    {
        parent::__construct();
        $this->searchProvider = $searchProvider;
    }

    public function getElement()
    {
        return 'users/search/menu';
    }

    public function setQuery(Query $query): void
    {
        $this->query = $query;
    }

    /**
     * @param mixed $exportURL
     */
    public function setExportURL($exportURL): void
    {
        $this->exportURL = $exportURL;
    }

    public function view()
    {
        $itemsPerPage = (isset($this->query)) ? $this->query->getItemsPerPage() : $this->searchProvider->getItemsPerPage();
        $this->set('itemsPerPage', $itemsPerPage);
        $this->set('itemsPerPageOptions', $this->searchProvider->getItemsPerPageOptions());
        $this->set('form', $this->app->make(Form::class));
        $this->set('token', $this->app->make(Token::class));
        $this->set('urlHelper', $this->app->make(Url::class));
        $this->set('exportURL', $this->exportURL);
    }

}
