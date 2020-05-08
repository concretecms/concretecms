<?php
namespace Concrete\Controller\Element\Files\Search;

use Concrete\Core\Controller\ElementController;
use Concrete\Core\Entity\Search\Query;
use Concrete\Core\File\Search\SearchProvider;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Tree\Node\Type\FileFolder;
use Concrete\Core\Utility\Service\Url;
use Concrete\Core\Validation\CSRF\Token;

class Menu extends ElementController
{

    /**
     * @var Query
     */
    protected $query;

    /**
     * @var SearchProvider
     */
    protected $searchProvider;

    /**
     * @var FileFolder
     */
    protected $currentFolder;

    public function __construct(SearchProvider $searchProvider)
    {
        $this->searchProvider = $searchProvider;
    }

    public function getElement()
    {
        return 'files/search/menu';
    }

    /**
     * @param Query $query
     */
    public function setQuery(Query $query): void
    {
        $this->query = $query;
    }

    /**
     * @param FileFolder $currentFolder
     */
    public function setCurrentFolder(FileFolder $currentFolder): void
    {
        $this->currentFolder = $currentFolder;
    }

    public function view()
    {
        $itemsPerPage = (isset($this->query)) ? $this->query->getItemsPerPage() :
            $this->searchProvider->getItemsPerPage();
        $this->set('itemsPerPage', $itemsPerPage);
        $this->set('itemsPerPageOptions', $this->searchProvider->getItemsPerPageOptions());
        $this->set('currentFolder', $this->currentFolder);
        $this->set('form', $this->app->make(Form::class));
        $this->set('token', $this->app->make(Token::class));
        $this->set('urlHelper', $this->app->make(Url::class));
    }

}
