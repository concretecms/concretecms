<?php /** @noinspection DuplicatedCode */

namespace Concrete\Controller\SinglePage\Dashboard\Reports;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\Search\Query;
use Concrete\Core\Entity\Search\SavedLogSearch;
use Concrete\Core\Filesystem\Element;
use Concrete\Core\Filesystem\ElementManager;
use Concrete\Core\Http\Response;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LogEntry;
use Concrete\Core\Logging\LogList;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Logging\Search\Menu\MenuFactory;
use Concrete\Core\Logging\Search\SearchProvider;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Search\Field\Field\KeywordsField;
use Concrete\Core\Search\Query\Modifier\AutoSortColumnRequestModifier;
use Concrete\Core\Search\Query\Modifier\ItemsPerPageRequestModifier;
use Concrete\Core\Search\Query\QueryFactory;
use Concrete\Core\Search\Query\QueryModifier;
use Concrete\Core\Search\Result\Result;
use Concrete\Core\Search\Result\ResultFactory;
use Concrete\Core\User\UserInfo;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\Request;

class Logs extends DashboardPageController
{

    /**
     * @var Element
     */
    protected $headerMenu;

    /**
     * @var Element
     */
    protected $headerSearch;

    /**
     * @return SearchProvider
     */
    protected function getSearchProvider()
    {
        return $this->app->make(SearchProvider::class);
    }

    /**
     * @return QueryFactory
     */
    protected function getQueryFactory()
    {
        return $this->app->make(QueryFactory::class);
    }

    protected function getHeaderMenu()
    {
        if (!isset($this->headerMenu)) {
            $this->headerMenu = $this->app->make(ElementManager::class)->get('dashboard/reports/logs/search/menu');
        }

        return $this->headerMenu;
    }

    protected function getHeaderSearch()
    {
        if (!isset($this->headerSearch)) {
            $this->headerSearch = $this->app->make(ElementManager::class)->get('dashboard/reports/logs/search/search');
        }

        return $this->headerSearch;
    }

    /**
     * @param Result $result
     */
    protected function renderSearchResult(Result $result)
    {
        $headerMenu = $this->getHeaderMenu();
        $headerSearch = $this->getHeaderSearch();
        /** @noinspection PhpPossiblePolymorphicInvocationInspection */
        $headerMenu->getElementController()->setQuery($result->getQuery());
        /** @noinspection PhpPossiblePolymorphicInvocationInspection */
        $headerSearch->getElementController()->setQuery($result->getQuery());

        $this->set('resultsBulkMenu', $this->app->make(MenuFactory::class)->createBulkMenu());
        $this->set('result', $result);
        $this->set('headerMenu', $headerMenu);
        $this->set('headerSearch', $headerSearch);

        $this->setThemeViewTemplate('full.php');
    }

    /**
     * @param Query $query
     * @return Result
     */
    protected function createSearchResult(Query $query)
    {
        $provider = $this->app->make(SearchProvider::class);
        /** @var ResultFactory $resultFactory */
        $resultFactory = $this->app->make(ResultFactory::class);
        /** @var QueryModifier $queryModifier */
        $queryModifier = $this->app->make(QueryModifier::class);
        $queryModifier->addModifier(new AutoSortColumnRequestModifier($provider, $this->request, Request::METHOD_GET));
        $queryModifier->addModifier(new ItemsPerPageRequestModifier($provider, $this->request, Request::METHOD_GET));
        $query = $queryModifier->process($query);
        return $resultFactory->createFromQuery($provider, $query);
    }

    protected function getSearchKeywordsField()
    {
        $keywords = null;

        if ($this->request->query->has('keywords')) {
            $keywords = $this->request->query->get('keywords');
        }

        return new KeywordsField($keywords);
    }

    public function view()
    {
        $query = $this->getQueryFactory()->createQuery($this->getSearchProvider(), [
            $this->getSearchKeywordsField()
        ]);
        $result = $this->createSearchResult($query);
        $this->renderSearchResult($result);
        /** @noinspection PhpPossiblePolymorphicInvocationInspection */
        $this->headerSearch->getElementController()->setQuery(null);
    }

    public function advanced_search()
    {
        $query = $this->getQueryFactory()->createFromAdvancedSearchRequest(
            $this->getSearchProvider(), $this->request, Request::METHOD_GET
        );

        $result = $this->createSearchResult($query);

        $this->renderSearchResult($result);
    }

    public function preset($presetID = null)
    {
        if ($presetID) {
            $preset = $this->entityManager->find(SavedLogSearch::class, $presetID);

            if ($preset) {
                /** @noinspection PhpParamsInspection */
                $query = $this->getQueryFactory()->createFromSavedSearch($preset);
                $result = $this->createSearchResult($query);
                $this->renderSearchResult($result);
                return;
            }
        }

        $this->view();
    }

    /** @noinspection PhpInconsistentReturnPointsInspection */
    public function export()
    {
        if ($this->isReportEnabled()) {
            $taskPermission = Key::getByHandle("delete_log_entries");
            if (is_object($taskPermission)) {
                $allowExport = $taskPermission->validate();
            } else {
                // This is a previous Concrete versions that don't have the new task permission installed
                $allowExport = true;
            }

            if ($allowExport) {
                /** @var ResponseFactory $responseFactory */
                $responseFactory = $this->app->make(ResponseFactory::class);
                /** @var Date $dateService */
                $dateService = $this->app->make(Date::class);

                $list = new LogList();
                /** @var LogEntry[] $entries */
                $entries = $list->getResults();

                $fp = fopen('php://temp', 'r+');

                // write the columns
                fputcsv($fp, [
                    t('Date'),
                    t('Level'),
                    t('Channel'),
                    t('User'),
                    t('Message'),
                ]);

                foreach ($entries as $entry) {
                    /** @noinspection PhpUnhandledExceptionInspection */
                    fputcsv($fp, [
                        $dateService->formatDateTime($entry->getTime(), true, true),
                        Logger::getLevelName($entry->getLevel()),
                        Channels::getChannelDisplayName($entry->getChannel()),
                        $entry->getUser() instanceof UserInfo ? $entry->getUser()->getUserName() : t('Guest'),
                        $entry->getMessage(),
                    ]);
                }

                rewind($fp);

                $csv = stream_get_contents($fp);

                fclose($fp);

                return $responseFactory->create($csv, Response::HTTP_OK, [
                    'Content-Type' => 'text/csv',
                    'Cache-control' => 'private',
                    'Pragma' => 'public',
                    'Content-Disposition' => 'attachment; filename="' . t('Log Search Results') . '_form_data_' . date('Ymd') . '.csv"'
                ]);
            }
        }
    }

    protected function isReportEnabled()
    {
        /** @var Repository $config */
        $config = $this->app->make(Repository::class);
        return $config->get('concrete.log.enable_dashboard_report');
    }

}
