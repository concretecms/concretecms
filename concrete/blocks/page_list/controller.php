<?php
namespace Concrete\Block\PageList;

use BlockType;
use CollectionAttributeKey;
use Concrete\Attribute\Topics\Controller as TopicsController;
use Concrete\Core\Api\ApiResourceValueInterface;
use Concrete\Core\Api\ApiValueSchemaInterface;
use Concrete\Core\Api\Block\ApiValueSchemaFactory;
use Concrete\Core\Attribute\Category\PageCategory;
use Concrete\Core\Attribute\Key\CollectionKey;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Block\Controller\SaveMode;
use Concrete\Core\Block\ExportDeclarations;
use Concrete\Core\Block\Traits\CustomApiValueTrait;
use Concrete\Core\Block\View\BlockView;
use Concrete\Core\Feature\Features;
use Concrete\Core\Feature\UsesFeatureInterface;
use Concrete\Core\Html\Service\Seo;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\Feed;
use Concrete\Core\Page\PageList;
use Concrete\Core\Page\Search\ColumnSet\Column\CollectionVersionColumn;
use Concrete\Core\Page\Search\ColumnSet\Column\DateLastModifiedColumn;
use Concrete\Core\Page\Search\ColumnSet\Column\DatePublicColumn;
use Concrete\Core\Page\Search\ColumnSet\Column\RandomColumn;
use Concrete\Core\Page\Search\ColumnSet\Column\SitemapDisplayOrderColumn;
use Concrete\Core\Search\Pagination\PaginationFactory;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\Type\Topic;
use Concrete\Core\Tree\Tree;
use Concrete\Core\Url\SeoCanonical;
use Concrete\Core\Utility\Service\Xml;
use Core;
use Database;
use Page;
use SimpleXMLElement;

class Controller extends BlockController implements ApiResourceValueInterface, ApiValueSchemaInterface, UsesFeatureInterface
{
    use CustomApiValueTrait;

    /**
     * @var int|string|null
     */
    public $num;

    /**
     * @var string|null
     */
    public $orderBy;

    /**
     * @var int|string|null
     */
    public $cParentID;

    /**
     * @var bool|int|string|null
     */
    public $cThis;

    /**
     * @var bool|int|string|null
     */
    public $cThisParent;

    /**
     * @var bool|int|string|null
     */
    public $useButtonForLink;

    /**
     * @var string|null
     */
    public $buttonLinkText;

    /**
     * @var string|null
     */
    public $pageListTitle;

    /**
     * @var bool|int|string|null
     */
    public $filterByRelated;

    /**
     * @var bool|int|string|null
     */
    public $filterByCustomTopic;

    /**
     * @var string|null
     */
    public $filterDateOption;

    /**
     * @var int|string|null
     */
    public $filterDateDays;

    /**
     * @var string|null
     */
    public $filterDateStart;

    /**
     * @var string|null
     */
    public $filterDateEnd;

    /**
     * @var string|null
     */
    public $relatedTopicAttributeKeyHandle;

    /**
     * @var string|null
     */
    public $customTopicAttributeKeyHandle;

    /**
     * @var int|string|null
     */
    public $customTopicTreeNodeID;

    /**
     * @var bool|int|string|null
     */
    public $includeName;

    /**
     * @var bool|int|string|null
     */
    public $includeDescription;

    /**
     * @var bool|int|string|null
     */
    public $includeDate;

    /**
     * @var bool|int|string|null
     */
    public $includeAllDescendents;

    /**
     * @var bool|int|string|null
     */
    public $paginate;

    /**
     * @var bool|int|string|null
     */
    public $excludeCanonicalPaging;

    /**
     * @var bool|int|string|null
     */
    public $displayAliases;

    /**
     * @var bool|int|string|null
     */
    public $displaySystemPages;

    /**
     * @var bool|int|string|null
     */
    public $ignorePermissions;

    /**
     * @var bool|int|string|null
     */
    public $enableExternalFiltering;

    /**
     * @var bool|int|string|null
     */
    public $excludeCurrentPage;

    /**
     * @var int|string|null
     */
    public $ptID;

    /**
     * @var int|string|null
     */
    public $pfID;

    /**
     * @var int|string|null
     */
    public $truncateSummaries;

    /**
     * @var bool|int|string|null
     */
    public $displayFeaturedOnly;

    /**
     * @var string|null
     */
    public $noResultsMessage;

    /**
     * @var bool|int|string|null
     */
    public $displayThumbnail;

    /**
     * @var int|string|null
     */
    public $truncateChars;

    /**
     * @var string|null
     */
    public $titleFormat;

    protected $btTable = 'btPageList';
    protected $btInterfaceWidth = 700;
    protected $btInterfaceHeight = 525;
    protected $btExportPageColumns = ['cParentID'];
    protected $btExportPageTypeColumns = ['ptID'];
    protected $btExportPageFeedColumns = ['pfID'];
    protected $btCacheBlockRecord = true;
    protected $btCacheBlockOutput = null;
    protected $btCacheBlockOutputOnPost = true;
    protected $btCacheBlockOutputLifetime = 300;
    /** @var PageList|null */
    protected $list;

    /**
     * @deprecated What's deprecated is the "public" part.
     *
     * @var int|null
     */
    public $cID;

    /**
     * @deprecated What's deprecated is the "public" part.
     *
     * @var int|null
     */
    public $cPID;

    public function getRequiredFeatures(): array
    {
        return [
            Features::NAVIGATION,
        ];
    }

    /**
     * Used for localization. If we want to localize the name/description we have to include this.
     */
    public function getBlockTypeDescription()
    {
        return t('List pages based on type, area.');
    }

    public function getBlockTypeName()
    {
        return t('Page List');
    }

    public function action_preview_pane()
    {
        $bt = BlockType::getByHandle($this->btHandle);
        $controller = $bt->getController();

        $this->request->query->set('num', ($this->request->get('num') > 0) ? 20 : 0);
        $this->request->query->set('cThis', ($this->request->get('cParentID') == $this->request->get('current_page')) ? '1' : '0');
        $this->request->query->set('cParentID', ($this->request->get('cParentID') === 'OTHER') ? $this->request->get('cParentIDValue') : $this->request->get('cParentID'));

        if ($this->request->get('filterDateOption') !== 'between') {
            $this->request->query->set('filterDateStart', null);
            $this->request->query->set('filterDateEnd', null);
        }

        if ($this->request->get('filterDateOption') === 'past') {
            $this->request->query->set('filterDateDays', $this->request->get('filterDatePast'));
        } elseif ($this->request->get('filterDateOption') === 'future') {
            $this->request->query->set('filterDateDays', $this->request->get('filterDateFuture'));
        } else {
            $this->request->query->set('filterDateDays', null);
        }

        $controller->num = $this->request->get('num');
        $controller->cParentID = $this->request->get('cParentID');
        $controller->cThis = $this->request->get('cThis');
        $controller->orderBy = $this->request->get('orderBy');
        $controller->ptID = $this->request->get('ptID');
        $controller->rss = $this->request->get('rss');
        $controller->displayFeaturedOnly = $this->request->get('displayFeaturedOnly') ?? false;
        $controller->displayAliases = $this->request->get('displayAliases') ?? false;
        $controller->paginate = $this->request->get('paginate') ?? false;
        $controller->excludeCanonicalPaging = $this->request->get('excludeCanonicalPaging') ?? false;
        $controller->enableExternalFiltering = $this->request->get('enableExternalFiltering') ?? false;
        $controller->excludeCurrentPage = $this->request->get('excludeCurrentPage') ?? false;
        $controller->filterByRelated = $this->request->get('filterByRelated') ?? false;
        $controller->relatedTopicAttributeKeyHandle = $this->request->get('relatedTopicAttributeKeyHandle');
        $controller->filterByCustomTopic = ($this->request->get('topicFilter') == 'custom') ? '1' : '0';
        $controller->customTopicAttributeKeyHandle = $this->request->get('customTopicAttributeKeyHandle');
        $controller->customTopicTreeNodeID = $this->request->get('customTopicTreeNodeID');
        $controller->includeAllDescendents = $this->request->get('includeAllDescendents') ?? false;
        $controller->includeDate = $this->request->get('includeDate') ?? false;
        $controller->displayThumbnail = $this->request->get('displayThumbnail') ?? false;
        $controller->includeDescription = $this->request->get('includeDescription') ?? false;
        $controller->useButtonForLink = $this->request->get('useButtonForLink') ?? false;
        $controller->filterDateOption = $this->request->get('filterDateOption');
        $controller->filterDateStart = $this->request->get('filterDateStart');
        $controller->filterDateEnd = $this->request->get('filterDateEnd');
        $controller->filterDateDays = $this->request->get('filterDateDays');
        $controller->noResultsMessage = $this->request->get('noResultsMessage');
        $controller->set('includeEntryText', true);
        $controller->set('includeName', true);
        $controller->set('displayThumbnail', $controller->displayThumbnail);
        $controller->set('noResultsMessage', $controller->noResultsMessage);
        $bv = new BlockView($bt);
        ob_start();
        $bv->render('view');
        $content = ob_get_contents();
        ob_end_clean();

        return $this->app->make(ResponseFactoryInterface::class)->create($content);
    }


    public function on_start()
    {
        $this->list = new PageList();
        $this->list->disableAutomaticSorting();
        $this->list->setNameSpace('b' . $this->bID);
        $expr = $this->list->getQueryObject()->expr(); // Get Query Expression Object

        $cArray = [];

        switch ($this->orderBy) {
            case 'display_asc':
                $sortColumn = new SitemapDisplayOrderColumn();
                $sortColumn->setColumnSortDirection('asc');
                $this->list->sortBySearchColumn($sortColumn);
                break;
            case 'display_desc':
                $sortColumn = new SitemapDisplayOrderColumn();
                $sortColumn->setColumnSortDirection('desc');
                $this->list->sortBySearchColumn($sortColumn);
                break;
            case 'chrono_asc':
                $sortColumn = new DatePublicColumn();
                $sortColumn->setColumnSortDirection('asc');
                $this->list->sortBySearchColumn($sortColumn);
                break;
            case 'modified_desc':
                $sortColumn = new DateLastModifiedColumn();
                $sortColumn->setColumnSortDirection('desc');
                $this->list->sortBySearchColumn($sortColumn);
                break;
            case 'random':
                $sortColumn = new RandomColumn();
                $this->list->sortBySearchColumn($sortColumn);
                $this->list->sortBy('RAND(' . rand() . ')');
                break;
            case 'alpha_asc':
                $sortColumn = new CollectionVersionColumn();
                $sortColumn->setColumnSortDirection('asc');
                $this->list->sortBySearchColumn($sortColumn);
                break;
            case 'alpha_desc':
                $sortColumn = new CollectionVersionColumn();
                $sortColumn->setColumnSortDirection('desc');
                $this->list->sortBySearchColumn($sortColumn);
                break;
            default:
                $sortColumn = new DatePublicColumn();
                $sortColumn->setColumnSortDirection('desc');
                $this->list->sortBySearchColumn($sortColumn);
                break;
        }

        $now = Core::make('helper/date')->toDB();
        $end = $start = null;

        switch ($this->filterDateOption) {
            case 'now':
                $start = date('Y-m-d') . ' 00:00:00';
                $end = $now;
                break;

            case 'past':
                $end = $now;

                if ($this->filterDateDays > 0) {
                    $past = date('Y-m-d', strtotime("-{$this->filterDateDays} days"));
                    $start = "$past 00:00:00";
                }
                break;

            case 'future':
                $start = $now;

                if ($this->filterDateDays > 0) {
                    $future = date('Y-m-d', strtotime("+{$this->filterDateDays} days"));
                    $end = "$future 23:59:59";
                }
                break;

            case 'between':
                if (!empty($this->filterDateStart)) {
                    $start = "{$this->filterDateStart} 00:00:00";
                }
                if (!empty($this->filterDateEnd)) {
                    $end = "{$this->filterDateEnd} 23:59:59";
                }
                break;

            case 'all':
            default:
                break;
        }

        if ($start) {
            $this->list->filterByPublicDate($start, '>=');
        }
        if ($end) {
            $this->list->filterByPublicDate($end, '<=');
        }

        $c = Page::getCurrentPage();
        if (is_object($c)) {
            $this->cID = $c->getCollectionID();
            $this->cPID = $c->getCollectionParentID();
        }

        if ($this->displayFeaturedOnly == 1) {
            if ($this->checkSearchablePageAttributeKey('is_featured') === '') {
                $this->list->filterByIsFeatured(1);
            }
        }
        if ($this->displayAliases) {
            $this->list->includeAliases();
        }
        if ($this->displaySystemPages) {
            $this->list->includeSystemPages();
        }
        if (isset($this->ignorePermissions) && $this->ignorePermissions) {
            $this->list->ignorePermissions();
        }
        if ($this->excludeCurrentPage) {
            $ID = Page::getCurrentPage()->getCollectionID();
            $this->list->getQueryObject()->andWhere($expr->neq('p.cID', $ID));
        }

        $this->list->filter('cvName', '', '!=');

        if ($this->ptID) {
            $this->list->filterByPageTypeID($this->ptID);
        }

        if ($this->filterByRelated) {
            $ak = CollectionKey::getByHandle($this->relatedTopicAttributeKeyHandle);
            if (is_object($ak)) {
                $topics = $c->getAttribute($ak->getAttributeKeyHandle());
                if (is_array($topics) && count($topics) > 0) {
                    $topic = $topics[array_rand($topics)];
                    $this->list->filter('p.cID', $c->getCollectionID(), '<>');
                    $this->list->filterByTopic($topic);
                }
            }
        }

        if ($this->filterByCustomTopic) {
            $ak = CollectionKey::getByHandle($this->customTopicAttributeKeyHandle);
            if (is_object($ak)) {
                $topic = Node::getByID($this->customTopicTreeNodeID);
                if ($topic) {
                    $ak->getController()->filterByAttribute($this->list, $this->customTopicTreeNodeID);
                }
            }
        }

        $this->list->filterByExcludePageList(false);

        if ((int) ($this->cParentID) !== 0 || (int) ($this->cThis) !== 0 || (int) ($this->cThisParent) !== 0) {
            $cParentID = ($this->cThis) ? $this->cID : (($this->cThisParent) ? $this->cPID : $this->cParentID);
            if ($this->includeAllDescendents) {
                $this->list->filterByPath(Page::getByID($cParentID)->getCollectionPath());
            } else {
                $this->list->filterByParentID($cParentID);
            }
        }

        if ($this->paginate && !$this->excludeCanonicalPaging) {
            $paging = $this->request->request($this->list->getQueryPaginationPageParameter());
            if ($paging && $paging >= 2) { // Canonicalize page 2 and greater only
                /** @var SeoCanonical $seoCanonical */
                $seoCanonical = $this->app->make(SeoCanonical::class);
                $seoCanonical->addIncludedQuerystringParameter($this->list->getQueryPaginationPageParameter());
            }
        }

        return $this->list;
    }

    public function view()
    {
        $list = $this->list;
        $nh = Core::make('helper/navigation');
        $this->set('nh', $nh);

        if ($this->pfID) {
            $this->requireAsset('css', 'font-awesome');
            $feed = Feed::getByID($this->pfID);
            if (is_object($feed)) {
                $this->set('rssUrl', $feed->getFeedURL());
                $link = $feed->getHeadLinkElement();
                $this->addHeaderItem($link);
            }
        }

        //Pagination...
        $showPagination = false;
        if ($this->num > 0) {
            $list->setItemsPerPage($this->num);
            $manager = $list->getPagerManager();
            $manager->sortListByCursor($list, $list->getActiveSortDirection());
            $paginationFactory = new PaginationFactory($this->request);
            $permissionedStylePagination = $this->paginate ? PaginationFactory::PERMISSIONED_PAGINATION_STYLE_FULL : PaginationFactory::PERMISSIONED_PAGINATION_STYLE_PAGER;
            $pagination = $paginationFactory->createPaginationObject($list, $permissionedStylePagination);
            $pages = $pagination->getCurrentPageResults();
            if ($pagination->haveToPaginate() && $this->paginate) {
                $showPagination = true;
                $pagination = $pagination->renderDefaultView();
                $this->set('pagination', $pagination);
            }
        } else {
            $pages = $list->getResults();
        }

        $this->set('pages', $pages);
        $this->set('list', $list);
        $this->set('showPagination', $showPagination);
    }

    public function add()
    {
        $c = Page::getCurrentPage();
        $uh = Core::make('helper/concrete/urls');
        $this->set('c', $c);
        $this->set('uh', $uh);
        $this->set('includeDescription', true);
        $this->set('includeName', true);
        $this->set('bt', BlockType::getByHandle($this->btHandle));
        $this->set('featuredAttributeUnusableReason', $this->checkSearchablePageAttributeKey('is_featured'));
        $this->set('thumbnailAttribute', CollectionAttributeKey::getByHandle('thumbnail'));
        $this->set('titleFormat', 'h5');
        $this->set('topicFilter', '');
        $this->set('filterDateOption', 'all');
        $this->set('num', 10);
        $this->set('ptID', 0);
        $this->set('customTopicAttributeKeyHandle', null);
        $this->set('relatedTopicAttributeKeyHandle', null);
        $this->set('customTopicTreeNodeID', 0);
        $this->set('filterDateDays', 0);
        $this->set('filterDateStart', null);
        $this->set('filterDateEnd', null);
        $this->set('displayFeaturedOnly', false);
        $this->set('displayAliases', false);
        $this->set('displaySystemPages', false);
        $this->set('ignorePermissions', false);
        $this->set('enableExternalFiltering', false);
        $this->set('excludeCurrentPage', false);
        $this->set('paginate', false);
        $this->set('excludeCanonicalPaging', false);
        $this->set('cParentID', 0);
        $this->set('cThis', false);
        $this->set('cThisParent', false);
        $this->set('isOtherPage', false);
        $this->set('includeAllDescendents', false);
        $this->set('orderBy', $this->orderBy);
        $this->set('rssFeed', false);
        $this->set('truncateSummaries', false);
        $this->set('truncateChars', 0);
        $this->set('includeDate', false);
        $this->set('displayThumbnail', false);
        $this->set('useButtonForLink', false);
        $this->set('buttonLinkText', null);
        $this->set('pageListTitle', false);
        $this->set('noResultsMessage', false);
        $this->loadKeys();
    }

    public function edit()
    {
        $b = $this->getBlockObject();
        $bID = $b->getBlockID();
        $this->set('bID', $bID);
        if ((!$this->cThis) && (!$this->cThisParent) && ($this->cParentID != 0)) {
            $this->set('isOtherPage', true);
        }
        if ($this->pfID) {
            $feed = Feed::getByID($this->pfID);
            if (is_object($feed)) {
                $this->set('rssFeed', $feed);
            }
        }
        $uh = Core::make('helper/concrete/urls');
        $this->set('uh', $uh);
        $this->set('bt', BlockType::getByHandle($this->btHandle));
        $this->set('featuredAttributeUnusableReason', $this->checkSearchablePageAttributeKey('is_featured'));
        $this->set('thumbnailAttribute', CollectionAttributeKey::getByHandle('thumbnail'));
        $topicFilter = '';
        if ($this->filterByRelated) {
            $topicFilter = 'related';
        }elseif ($this->filterByCustomTopic) {
            $topicFilter = 'custom';
        }
        $this->set('topicFilter', $topicFilter);
        $this->loadKeys();
    }

    public function action_filter_by_topic($treeNodeID = false, $topic = false)
    {
        if ($treeNodeID) {
            $topicObj = Topic::getByID((int) $treeNodeID);
            if (is_object($topicObj) && $topicObj instanceof Topic) {
                $this->list->filterByTopic((int) $treeNodeID);

                /** @var Seo $seo */
                $seo = $this->app->make('helper/seo');
                $seo->addTitleSegment($topicObj->getTreeNodeDisplayName());

                /** @var SeoCanonical $canonical */
                $canonical = $this->app->make(SeoCanonical::class);
                $canonical->setPathArguments(['topic', $treeNodeID, $topic]);
            }
        }
        $this->view();
    }

    public function action_filter_by_tag($tag = false)
    {
        /** @var Seo $seo */
        $seo = $this->app->make('helper/seo');
        $seo->addTitleSegment($tag);

        /** @var SeoCanonical $canonical */
        $canonical = $this->app->make(SeoCanonical::class);
        $canonical->setPathArguments(['tag', $tag]);

        $this->list->filterByTags(h($tag));
        $this->view();
    }

    public function action_search_keywords($bID)
    {
        if ($bID == $this->bID) {
            $keywords = h($this->request->query->get('keywords'));
            $this->list->filterByKeywords($keywords);
            $this->view();
        }
    }

    public function action_filter_by_date($year = false, $month = false, $timezone = 'user')
    {
        if (is_numeric($year)) {
            $year = (($year < 0) ? '-' : '') . str_pad(abs($year), 4, '0', STR_PAD_LEFT);
            if ($month) {
                $month = str_pad($month, 2, '0', STR_PAD_LEFT);
                $lastDayInMonth = date('t', strtotime("$year-$month-01"));
                $start = "$year-$month-01 00:00:00";
                $end = "$year-$month-$lastDayInMonth 23:59:59";
            } else {
                $start = "$year-01-01 00:00:00";
                $end = "$year-12-31 23:59:59";
            }
            $dh = Core::make('helper/date');
            /* @var $dh \Concrete\Core\Localization\Service\Date */
            if ($timezone !== 'system') {
                $start = $dh->toDB($start, $timezone);
                $end = $dh->toDB($end, $timezone);
            }
            $this->list->filterByPublicDate($start, '>=');
            $this->list->filterByPublicDate($end, '<=');

            /** @var Seo $seo */
            $seo = $this->app->make('helper/seo');
            $date = ucfirst(\Punic\Calendar::getMonthName($month, 'wide', '', true) . ' ' . $year);
            $seo->addTitleSegment($date);

            /** @var SeoCanonical $canonical */
            $canonical = $this->app->make(SeoCanonical::class);
            $canonical->setPathArguments([$year, $month]);
        }
        $this->view();
    }

    public function validate($args)
    {
        $args += [
            'rss' => null,
            'rssHandle' => '',
            'rssTitle' => '',
            'rssDescription' => ''
        ];

        $e = Core::make('helper/validation/error');
        $vs = Core::make('helper/validation/strings');
        $pf = false;
        if ($this->pfID) {
            $pf = Feed::getByID($this->pfID);
        }
        if ($args['rss'] && !is_object($pf)) {
            if (!$vs->alphanum($args['rssHandle'], false, true)) {
                $e->add(t('Your RSS feed must have a valid URL, containing only letters, numbers or hyphens'));
            }
            if (!$vs->notempty($args['rssTitle'])) {
                $e->add(t('Your RSS feed must have a valid title.'));
            }
            if (!$vs->notempty($args['rssDescription'])) {
                $e->add(t('Your RSS feed must have a valid description.'));
            }
        }

        return $e;
    }

    public function getPassThruActionAndParameters($parameters)
    {
        if ($parameters[0] == 'preview_pane') {
            return parent::getPassThruActionAndParameters($parameters);
        }

        if ($parameters[0] == 'topic') {
            $method = 'action_filter_by_topic';
            $parameters = array_slice($parameters, 1);
        } elseif ($parameters[0] == 'tag') {
            $method = 'action_filter_by_tag';
            $parameters = array_slice($parameters, 1);
        } elseif (Core::make('helper/validation/numbers')->integer($parameters[0])) {
            // then we're going to treat this as a year.
            $method = 'action_filter_by_date';
            $parameters[0] = (int)($parameters[0]);
            if (isset($parameters[1])) {
                $parameters[1] = (int)($parameters[1]);
            }
        } else if ($parameters[0] == 'search_keywords') {
            return parent::getPassThruActionAndParameters($parameters);
        } else {
            $parameters = $method = null;
        }

        return [$method, $parameters];
    }

    public function isValidControllerTask($method, $parameters = [])
    {
        if ($method == 'action_preview_pane') {
            return true;
        }

        if (!$this->enableExternalFiltering) {
            return false;
        }

        if ($method === 'action_filter_by_date') {
            // Parameter 0 must be set
            if (!isset($parameters[0]) || $parameters[0] < 0 || $parameters[0] > 9999) {
                return false;
            }
            // Parameter 1 can be null
            if (isset($parameters[1])) {
                if ($parameters[1] < 1 || $parameters[1] > 12) {
                    return false;
                }
            }
        }

        return parent::isValidControllerTask($method, $parameters);
    }

    public function save($args)
    {
        $fromCIF = $this->saveMode === SaveMode::SAVE_MODE_IMPORT;

        // If we've gotten to the process() function for this class, we assume that we're in
        // the clear, as far as permissions are concerned (since we check permissions at several
        // points within the dispatcher)
        $db = Database::connection();

        $bID = $this->bID;
        $c = $this->getCollectionObject();
        if (is_object($c)) {
            $this->cID = $c->getCollectionID();
            $this->cPID = $c->getCollectionParentID();
        }

        $args += [
            'enableExternalFiltering' => 0,
            'includeAllDescendents' => 0,
            'includeDate' => 0,
            'truncateSummaries' => 0,
            'displayFeaturedOnly' => 0,
            'topicFilter' => '',
            'displayThumbnail' => 0,
            'displayAliases' => 0,
            'displaySystemPages' => 0,
            'excludeCurrentPage' => 0,
            'truncateChars' => 0,
            'paginate' => 0,
            'excludeCanonicalPaging' => 0,
            'rss' => 0,
            'pfID' => 0,
            'ptID' => 0,
            'filterDateOption' => 'all',
            'cParentID' => null,
            'ignorePermissions' => 0,
        ];

        if (is_numeric($args['cParentID'])) {
            $args['cParentID'] = (int) ($args['cParentID']);
        }

        $args['num'] = ($args['num'] > 0) ? $args['num'] : 0;
        if (!$fromCIF) {
            $args['cThis'] = ($args['cParentID'] === $this->cID) ? '1' : '0';
            $args['cThisParent'] = ($args['cParentID'] === $this->cPID) ? '1' : '0';
            $args['cParentID'] = ($args['cParentID'] === 'OTHER') ? (empty($args['cParentIDValue']) ? null : $args['cParentIDValue']) : $args['cParentID'];
            if (!$args['cParentID']) {
                $args['cParentID'] = 0;
            }
            $args['filterByRelated'] = ($args['topicFilter'] == 'related') ? '1' : '0';
            $args['filterByCustomTopic'] = ($args['topicFilter'] == 'custom') ? '1' : '0';
            if (!$args['filterByCustomTopic'] || !$this->app->make('helper/number')->isInteger($args['customTopicTreeNodeID'])) {
                $args['customTopicAttributeKeyHandle'] = '';
                $args['customTopicTreeNodeID'] = 0;
            }
        }
        $args['enableExternalFiltering'] = ($args['enableExternalFiltering']) ? '1' : '0';
        $args['includeAllDescendents'] = ($args['includeAllDescendents']) ? '1' : '0';
        $args['includeDate'] = ($args['includeDate']) ? '1' : '0';
        $args['truncateSummaries'] = ($args['truncateSummaries']) ? '1' : '0';
        $args['displayFeaturedOnly'] = ($args['displayFeaturedOnly']) ? '1' : '0';
        $args['displayThumbnail'] = ($args['displayThumbnail']) ? '1' : '0';
        $args['displayAliases'] = ($args['displayAliases']) ? '1' : '0';
        $args['displaySystemPages'] = ($args['displaySystemPages']) ? '1' : '0';
        $args['excludeCurrentPage'] = ($args['excludeCurrentPage']) ? '1' : '0';
        $args['truncateChars'] = (int) ($args['truncateChars']);
        $args['paginate'] = (int) ($args['paginate']);
        $args['excludeCanonicalPaging'] = (int) ($args['excludeCanonicalPaging'] ?? 0);
        $args['rss'] = (int) ($args['rss']);
        $args['ptID'] = (int) ($args['ptID']);

        if (!$args['filterByRelated']) {
            $args['relatedTopicAttributeKeyHandle'] = '';
        }

        if ($args['rss']) {
            if ($fromCIF) {
                $pfID = (int) ($args['pfID'] ?? 0);
            } else {
                $pfID = (int) $this->pfID;
            }
            $pf = $pfID === 0 ? null : Feed::getByID($pfID);
            if (!$pf) {
                $pf = new \Concrete\Core\Entity\Page\Feed();
            }
            if ((string) ($args['rssHandle'] ?? '') !== '') {
                $pf->setHandle($args['rssHandle']);
            }
            if ((string) ($args['rssTitle'] ?? '') !== '') {
                $pf->setTitle($args['rssTitle']);
            }
            $pf->setDescription($args['rssDescription'] ?? '');
            $pf->setParentID($args['cParentID']);
            $pf->setPageTypeID($args['ptID']);
            $pf->setIncludeAllDescendents($args['includeAllDescendents']);
            $pf->setDisplayAliases($args['displayAliases']);
            $pf->setDisplayFeaturedOnly($args['displayFeaturedOnly']);
            $pf->setDisplaySystemPages($args['displaySystemPages']);
            $pf->displayShortDescriptionContent();
            $pf->save();
            $args['pfID'] = $pf->getID();
        } elseif (isset($this->pfID) && $this->pfID && !$args['rss']) {
            // let's make sure this isn't in use elsewhere.
            $cnt = $db->fetchColumn('select count(pfID) from btPageList where pfID = ?', [$this->pfID]);
            if ($cnt == 1) { // this is the last one, so we delete
                $pf = Feed::getByID($this->pfID);
                if (is_object($pf)) {
                    $pf->delete();
                }
            }
            $args['pfID'] = 0;
        }

        if ($args['filterDateOption'] != 'between') {
            $args['filterDateStart'] = null;
            $args['filterDateEnd'] = null;
        }

        if ($args['filterDateOption'] == 'past') {
            $args['filterDateDays'] = $args['filterDateDays'] ?? $args['filterDatePast'];
        } elseif ($args['filterDateOption'] == 'future') {
            $args['filterDateDays'] = $args['filterDateDays'] ?? $args['filterDateFuture'];
        } else {
            $args['filterDateDays'] = null;
        }

        $args['pfID'] = (int) $args['pfID'];
        parent::save($args);
    }

    public function isBlockEmpty()
    {
        $pages = $this->get('pages');
        if (isset($this->pageListTitle) && $this->pageListTitle) {
            return false;
        }
        if (empty($pages)) {
            if ($this->noResultsMessage) {
                return false;
            }

            return true;
        }
        if ($this->includeName || $this->includeDate || $this->displayThumbnail
                || $this->includeDescription || $this->useButtonForLink
            ) {
            return false;
        }

        return true;
    }

    public function cacheBlockOutput()
    {
        if ($this->btCacheBlockOutput === null) {
            if (!$this->enableExternalFiltering && !$this->paginate) {
                $this->btCacheBlockOutput = true;
            } else {
                $this->btCacheBlockOutput = false;
            }
        }

        return  $this->btCacheBlockOutput;
    }

    protected function loadKeys()
    {
        $attributeKeys = [];
        $keys = CollectionKey::getList();
        foreach ($keys as $ak) {
            if ($ak->getAttributeTypeHandle() == 'topics') {
                $attributeKeys[] = $ak;
            }
        }
        $this->set('attributeKeys', $attributeKeys);
    }

    /**
     * @return string the reason why the attribute key is not usable (or an empty string if it's usable)
     */
    protected function checkSearchablePageAttributeKey(string $handle): string
    {
        $category = $this->app->make(PageCategory::class);
        $key = $category->getAttributeKeyByHandle($handle);
        if ($key === null) {
            return t('You must create the %s page attribute first.', "<code>{$handle}</code>");
        }
        if (!$key->isAttributeKeyContentIndexed()) {
            return t('The %s page attribute must be indexed.', "<code>{$handle}</code>");
        }

        return '';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::export()
     */
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Api\ApiValueSchemaInterface::getApiValueSchema()
     */
    public function getApiValueSchema(): array
    {
        $schemaFactory = $this->app->make(ApiValueSchemaFactory::class);

        return [
            'type' => 'object',
            'properties' => [
                'cParentID' => $schemaFactory->describeReference(ExportDeclarations::REFERENCE_PAGE, [
                    'type' => ['string', 'integer'],
                    'description' => 'The page whose children are listed (0 for the whole site): it\'s used only when both cThis and cThisParent are 0.',
                ]),
                'cThis' => [
                    'type' => ['boolean', 'string', 'integer'],
                    'description' => 'Set it to true to list the children of the page holding the block.',
                ],
                'cThisParent' => [
                    'type' => ['boolean', 'string', 'integer'],
                    'description' => 'Set it to true to list the children of the parent of the page holding the block.',
                ],
                'includeAllDescendents' => [
                    'type' => ['boolean', 'string', 'integer'],
                    'description' => 'Set it to true to list the pages at any depth below the chosen page, instead of its children only.',
                ],
                'excludeCurrentPage' => [
                    'type' => ['boolean', 'string', 'integer'],
                    'description' => 'Set it to true to leave out the page holding the block.',
                ],
                'displayAliases' => [
                    'type' => ['boolean', 'string', 'integer'],
                    'description' => 'Set it to true to list the aliases of the pages too.',
                ],
                'displaySystemPages' => [
                    'type' => ['boolean', 'string', 'integer'],
                    'description' => 'Set it to true to list the pages of the system too.',
                ],
                'displayFeaturedOnly' => [
                    'type' => ['boolean', 'string', 'integer'],
                    'description' => 'Set it to true to list only the pages whose is_featured attribute is set.',
                ],
                'ignorePermissions' => [
                    'type' => ['boolean', 'string', 'integer'],
                    'description' => 'Set it to true to list the pages the visitors can\'t see too.',
                ],
                'ptID' => $schemaFactory->describeReference(ExportDeclarations::REFERENCE_PAGE_TYPE, [
                    'type' => ['string', 'integer', 'null'],
                    'description' => 'The type the listed pages must have (0 for any type).',
                ]),
                'filterByRelated' => [
                    'type' => ['boolean', 'string', 'integer'],
                    'description' => 'Set it to true to list only the pages sharing a topic with the page holding the block.',
                ],
                'relatedTopicAttributeKeyHandle' => [
                    'type' => ['string', 'null'],
                    'maxLength' => 255,
                    'description' => 'The handle of the topics attribute holding the topics to be shared (it\'s used only when filterByRelated is 1).',
                ],
                'filterByCustomTopic' => [
                    'type' => ['boolean', 'string', 'integer'],
                    'description' => 'Set it to true to list only the pages assigned to the customTopicTreeNodeID topic.',
                ],
                'customTopicAttributeKeyHandle' => [
                    'type' => ['string', 'null'],
                    'maxLength' => 255,
                    'description' => 'The handle of the topics attribute of the pages holding the topic (it\'s used only when filterByCustomTopic is 1).',
                ],
                'customTopicTreeNodeID' => [
                    'type' => ['string', 'integer'],
                    'description' => 'The ID of the topic the listed pages must be assigned to (0 for none): when it\'s not a topic of the customTopicAttributeKeyHandle attribute, the filter is turned off.',
                ],
                'filterDateOption' => [
                    'type' => ['string', 'null'],
                    'enum' => ['all', 'now', 'past', 'future', 'between'],
                    'default' => 'all',
                    'description' => 'Which pages are listed, by the date they were published: all of them, the ones of today, the ones published in the last filterDateDays days, the ones to be published in the next filterDateDays days, or the ones published between filterDateStart and filterDateEnd.',
                ],
                'filterDateDays' => [
                    'type' => ['string', 'integer'],
                    'description' => 'The number of days (it\'s used only when filterDateOption is "past" or "future"; 0 for no limit).',
                ],
                'filterDateStart' => [
                    'type' => ['string', 'null'],
                    'description' => 'The first day the pages can be published in, as YYYY-MM-DD (it\'s used only when filterDateOption is "between").',
                ],
                'filterDateEnd' => [
                    'type' => ['string', 'null'],
                    'description' => 'The last day the pages can be published in, as YYYY-MM-DD (it\'s used only when filterDateOption is "between").',
                ],
                'enableExternalFiltering' => [
                    'type' => ['boolean', 'string', 'integer'],
                    'description' => 'Set it to true to let the query string of the page filter the listed pages too.',
                ],
                'orderBy' => [
                    'type' => ['string', 'null'],
                    'enum' => ['display_asc', 'display_desc', 'chrono_desc', 'chrono_asc', 'alpha_asc', 'alpha_desc', 'modified_desc', 'random'],
                    'description' => 'The order the pages are listed in.',
                ],
                'num' => [
                    'type' => ['string', 'integer'],
                    'description' => 'The number of pages to be listed (0 for all of them).',
                ],
                'paginate' => [
                    'type' => ['boolean', 'string', 'integer'],
                    'description' => 'Set it to true to split the pages in pages of num items.',
                ],
                'excludeCanonicalPaging' => [
                    'type' => ['boolean', 'string', 'integer'],
                    'description' => 'Set it to true to leave the paging out of the canonical URL of the page.',
                ],
                'pageListTitle' => [
                    'type' => ['string', 'null'],
                    'maxLength' => 255,
                    'description' => 'The text displayed above the list.',
                ],
                'titleFormat' => [
                    'type' => 'string',
                    'enum' => array_keys(BlockController::$btTitleFormats),
                    'default' => 'h5',
                    'description' => 'The HTML element wrapping the text displayed above the list.',
                ],
                'includeName' => [
                    'type' => ['boolean', 'string', 'integer'],
                    'description' => 'Set it to true to display the name of every listed page.',
                ],
                'includeDescription' => [
                    'type' => ['boolean', 'string', 'integer'],
                    'description' => 'Set it to true to display the description of every listed page.',
                ],
                'truncateSummaries' => [
                    'type' => ['boolean', 'string', 'integer'],
                    'description' => 'Set it to true to cut the descriptions to truncateChars characters.',
                ],
                'truncateChars' => [
                    'type' => ['string', 'integer'],
                    'description' => 'The number of characters the descriptions are cut to (it\'s used only when truncateSummaries is 1).',
                ],
                'includeDate' => [
                    'type' => ['boolean', 'string', 'integer'],
                    'description' => 'Set it to true to display the date every listed page was published in.',
                ],
                'displayThumbnail' => [
                    'type' => ['boolean', 'string', 'integer'],
                    'description' => 'Set it to true to display the thumbnail of every listed page.',
                ],
                'useButtonForLink' => [
                    'type' => ['boolean', 'string', 'integer'],
                    'description' => 'Set it to true to link every listed page with a button, instead of its name.',
                ],
                'buttonLinkText' => [
                    'type' => ['string', 'null'],
                    'maxLength' => 255,
                    'description' => 'The text of the button (it\'s used only when useButtonForLink is 1).',
                ],
                'noResultsMessage' => [
                    'type' => ['string', 'null'],
                    'maxLength' => 255,
                    'description' => 'The text displayed when no page is listed.',
                ],
                'pfID' => $schemaFactory->describeReference(ExportDeclarations::REFERENCE_PAGE_FEED, [
                    'type' => ['string', 'integer', 'null'],
                    'description' => 'The RSS feed publishing the listed pages (0 for none). Setting the rssHandle key creates it, or updates the one this key refers to.',
                ]),
                'rssHandle' => [
                    'type' => 'string',
                    'description' => 'The handle of the RSS feed, which is part of its address: when this key is missing, the block publishes no feed.',
                ],
                'rssTitle' => [
                    'type' => 'string',
                    'description' => 'The title of the RSS feed.',
                ],
                'rssDescription' => [
                    'type' => 'string',
                    'description' => 'The description of the RSS feed.',
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::getImportDataFromApiValue()
     */
    public function getImportDataFromApiValue($page, array $value): array
    {
        if ($this->bID) {
            // the save() method resets the settings that it doesn't receive: let's keep the current ones
            $value += $this->serializeValueForApi();
        }
        $args = parent::getImportDataFromApiValue($page, $value);
        // the getImportData() method above resolves the path of the topic that a CIF file carries: the API
        // refers to the very same node by its ID
        $topicAttributeKeyHandle = (string) ($value['customTopicAttributeKeyHandle'] ?? '');
        $topicTreeNodeID = isset($value['customTopicTreeNodeID']) && is_numeric($value['customTopicTreeNodeID']) ? (int) $value['customTopicTreeNodeID'] : 0;
        if ($topicAttributeKeyHandle === '' || $topicTreeNodeID <= 0 || Node::getByID($topicTreeNodeID) === null) {
            $args['customTopicAttributeKeyHandle'] = '';
            $args['customTopicTreeNodeID'] = 0;
            $args['filterByCustomTopic'] = 0;
        } else {
            $args['customTopicAttributeKeyHandle'] = $topicAttributeKeyHandle;
            $args['customTopicTreeNodeID'] = $topicTreeNodeID;
            $args['filterByCustomTopic'] = empty($value['filterByCustomTopic']) ? 0 : 1;
        }

        return $args;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\Traits\CustomApiValueTrait::serializeValueForApi()
     */
    protected function serializeValueForApi(): array
    {
        // the export() method below replaces the ID of the topic with its path, since a CIF file can't refer
        // to it by its ID: the API can
        $blockNode = new SimpleXMLElement('<block></block>');
        $this->export($blockNode);
        $mainTable = (string) $this->getBlockTypeDatabaseTable();
        $result = [];
        foreach ($blockNode->data as $data) {
            if (strcasecmp((string) $data['table'], $mainTable) === 0 && isset($data->record)) {
                $result = $this->serializeRecordForApi($mainTable, $data->record);
                break;
            }
        }
        unset($result['customTopicTreeNodePath']);
        $result['customTopicTreeNodeID'] = (int) $this->customTopicTreeNodeID;

        return $result;
    }

    public function export(SimpleXMLElement $blockNode)
    {
        parent::export($blockNode);
        $xml = $this->app->make(Xml::class);
        $xRecord = $blockNode->data[0]->record[0];

        $customTopicTreeNode = null;
        $customTopicAttributeKeyHandle = (string) $this->customTopicAttributeKeyHandle;
        $customTopicTreeNodeID = (int) $this->customTopicTreeNodeID;
        if ($customTopicAttributeKeyHandle !== '' && $customTopicTreeNodeID > 0) {
            $customTopicTreeNode = Node::getByID($customTopicTreeNodeID);
        }
        unset($xRecord->customTopicTreeNodeID[0]);
        $xml->createChildElement($xRecord, 'customTopicTreeNodePath', $customTopicTreeNode ? $customTopicTreeNode->getTreeNodeDisplayPath() : '');
        $feed = $this->pfID ? Feed::getByID($this->pfID) : null;
        if ($feed) {
            $xml->createChildElement($xRecord, 'rssHandle', $feed->getHandle());
            $xml->createChildElement($xRecord, 'rssTitle', $feed->getTitle());
            $xml->createChildElement($xRecord, 'rssDescription', $feed->getDescription());
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::getImportData()
     */
    protected function getImportData($blockNode, $page)
    {
        $this->saveMode = SaveMode::SAVE_MODE_IMPORT;
        if (!$this->cID && $page) {
            $this->cID = $page->getCollectionID();
        }
        $args = parent::getImportData($blockNode, $page);
        $args['customTopicTreeNodeID'] = 0;
        $customTopicAttributeKeyHandle = (string) ($args['customTopicAttributeKeyHandle'] ?? '');
        $customTopicTreeNodePath = (string) ($args['customTopicTreeNodePath'] ?? '');
        if ($customTopicAttributeKeyHandle !== '' && $customTopicTreeNodePath !== '') {
            $topicAttributeKey = $this->app->make(PageCategory::class)->getAttributeKeyByHandle($customTopicAttributeKeyHandle);
            $topicController = $topicAttributeKey ? $topicAttributeKey->getController() : null;
            if ($topicController instanceof TopicsController) {
                $tree = Tree::getByID($topicController->getTopicTreeID());
                if ($tree instanceof Tree) {
                    $customTopicTreeNode = $tree->getNodeByDisplayPath($customTopicTreeNodePath);
                    if ($customTopicTreeNode) {
                        $args['customTopicTreeNodeID'] = $customTopicTreeNode->getTreeNodeID();
                    };
                }
            }
        }
        if ($args['customTopicTreeNodeID'] === 0) {
            $args['filterByCustomTopic'] = 0;
            $args['customTopicAttributeKeyHandle'] = '';
        } else {
            $args['filterByCustomTopic'] = empty($args['filterByCustomTopic']) ? 0 : 1;
            $args['customTopicAttributeKeyHandle'] = $customTopicAttributeKeyHandle;
        }
        $args['cThis'] = empty($args['cThis']) ? 0 : 1;
        $args['cThisParent'] = empty($args['cThisParent']) ? 0 : 1;
        $args['cParentID'] = empty($args['cParentID']) ? 0 : (int) $args['cParentID'];
        $args['filterByRelated'] = empty($args['filterByRelated']) ? 0 : 1;
        $args['filterByCustomTopic'] = empty($args['filterByCustomTopic']) ? 0 : 1;
        $feed = empty($args['pfID']) ? null : Feed::getByID($args['pfID']);
        $feedHandle = (string) ($args['rssHandle'] ?? '');
        if ($feed || $feedHandle !== '') {
            $args['rss'] = 1;
            $args['pfID'] = $feed ? $feed->getID() : null;
            $args['rssHandle'] = $feedHandle !== '' ? $feedHandle : $feed->getHandle();
            if ((string) ($args['rssTitle'] ?? '') === '') {
                $args['rssHandle'] = $feed ? $feed->getTitle() : $feedHandle;
            }
            if ((string) ($args['rssDescription'] ?? '') === '') {
                $args['rssDescription'] = $feed ? $feed->getDescription() : '';
            }
        }

        return $args;
    }
}
