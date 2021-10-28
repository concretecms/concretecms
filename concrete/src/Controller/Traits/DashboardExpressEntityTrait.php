<?php

namespace Concrete\Core\Controller\Traits;

use Concrete\Core\Attribute\Category\ExpressCategory;
use Concrete\Core\Csv\WriterFactory;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Search\Query;
use Concrete\Core\Entity\Search\SavedExpressSearch;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Express\Export\EntryList\CsvWriter;
use Concrete\Core\Express\Search\Field\SiteField;
use Concrete\Core\Express\Search\SearchProvider;
use Concrete\Core\Filesystem\Element;
use Concrete\Core\Filesystem\ElementManager;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\Navigation\Breadcrumb\Dashboard\DashboardExpressBreadcrumbFactory;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Search\Field\Field\KeywordsField;
use Concrete\Core\Search\Query\Modifier\AutoSortColumnRequestModifier;
use Concrete\Core\Search\Query\Modifier\ItemsPerPageRequestModifier;
use Concrete\Core\Search\Query\QueryFactory;
use Concrete\Core\Search\Query\QueryModifier;
use Concrete\Core\Search\Result\Result;
use Concrete\Core\Search\Result\ResultFactory;
use Concrete\Core\Site\InstallationService;
use Concrete\Core\Url\Url;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Adds specific Dashboard endpoints for viewing an entity's results and advanced search.
 */
trait DashboardExpressEntityTrait
{

    use DashboardExpressEntryListTrait;
    use DashboardExpressEntryDetailsTrait;

    protected function getBackURL(Entity $entity)
    {
        return $this->app->make('url')->to($this->getPageObject()->getCollectionPath());
    }

    abstract public function getExpressEntity() : Entity;

    protected function getHeaderSearchAction(Entity $entity)
    {
        return $this->app->make('url')->to($this->getPageObject()->getCollectionPath());
    }

    public function view()
    {
        $this->renderExpressEntryDefaultResults($this->getExpressEntity());
    }

    public function advanced_search($entityID = null)
    {
        // Note $entityID has to exist here because the advanced search controller passes it back as the
        // first argument to the `advanced_search` endpoint always.
        $this->renderExpressEntryAdvancedSearchResults($this->getExpressEntity());
    }

    public function csv_export($entityID = null, $searchMethod = null)
    {
        return $this->exportCsv($this->getExpressEntity(), $searchMethod);
    }

}
