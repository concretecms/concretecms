<?php
namespace Concrete\Block\Search;

use CollectionAttributeKey;
use Concrete\Core\Attribute\Key\CollectionKey;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Page\PageList;
use Core;
use Database;
use Page;
use Request;

class Controller extends BlockController
{
    /**
     * Search title.
     *
     * @var string
     */
    public $title = '';

    /**
     * Button text.
     *
     * @var string
     */
    public $buttonText = '>';

    /**
     * The base search path.
     *
     * @var string
     */
    public $baseSearchPath = '';

    /**
     * Destination page (another page).
     *
     * @var int|null
     */
    public $postTo_cID = null;

    /**
     * Destination page (external URL).
     *
     * @var string
     */
    public $resultsURL = '';

    /**
     * Unused?
     *
     * @var string[]
     */
    public $reservedParams = ['page=', 'query=', 'search_paths[]=', 'submit=', 'search_paths%5B%5D='];

    /**
     * The name of the database that holds the block data.
     *
     * @var string
     */
    protected $btTable = 'btSearch';

    /**
     * Set this to true if the data sent to the save/performSave methods can contain NULL values that should be persisted.
     *
     * @var bool
     */
    protected $supportSavingNullValues = true;

    /**
     * Add/Edit dialog width (in pixels).
     *
     * @var int
     */
    protected $btInterfaceWidth = 400;

    /**
     * Add/Edit dialog height (in pixels).
     *
     * @var int
     */
    protected $btInterfaceHeight = 420;

    /**
     * The CSS class of the block wrapper (unused?).
     *
     * @var string
     */
    protected $btWrapperClass = 'ccm-ui';

    /**
     * List of database table fields that contains fields with collection identifiers.
     *
     * @var string[]
     */
    protected $btExportPageColumns = ['postTo_cID'];

    /**
     * Should the database record be cached?
     *
     * @var bool
     */
    protected $btCacheBlockRecord = true;

    /**
     * Should the block output be cached?
     *
     * @var bool
     */
    protected $btCacheBlockOutput = null;

    /**
     * The color to be used to highlight results.
     *
     * @var string
     */
    protected $hColor = '#EFE795';

    /**
     * {@inheritdoc}
     */
    public function getBlockTypeName()
    {
        return t('Search');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockTypeDescription()
    {
        return t('Add a search box to your site.');
    }

    /**
     * Highlight parts of a text.
     *
     * @param string $fulltext the whole text
     * @param string $highlight The text to be highlighted
     *
     * @return string
     */
    public function highlightedMarkup($fulltext, $highlight)
    {
        if (!$highlight) {
            return $fulltext;
        }

        $this->hText = $fulltext;
        $this->hHighlight = $highlight;
        $this->hText = @preg_replace('#' . preg_quote($this->hHighlight, '#') . '#ui', '<span style="background-color:' . $this->hColor . ';">$0</span>', $this->hText);

        return $this->hText;
    }

    /**
     * Highlight parts of a text (extended version).
     *
     * @param string $fulltext the whole text
     * @param string $highlight The text to be highlighted
     *
     * @return string|null
     */
    public function highlightedExtendedMarkup($fulltext, $highlight)
    {
        $text = @preg_replace("#\n|\r#", ' ', $fulltext);

        $matches = [];
        $highlight = str_replace(['"', "'", '&quot;'], '', $highlight); // strip the quotes as they mess the regex

        if (!$highlight) {
            $text = $this->app->make('helper/text')->shorten($fulltext, 180);
            if (strlen($fulltext) > 180) {
                $text .= '&hellip;<wbr>';
            }
            $result = $text;
        } else {
            $result = null;
            $regex = '([[:alnum:]|\'|\.|_|\s]{0,45})' . preg_quote($highlight, '#') . '([[:alnum:]|\.|_|\s]{0,45})';
            preg_match_all("#$regex#ui", $text, $matches);

            if (!empty($matches[0])) {
                $body_length = 0;
                $body_string = [];
                foreach ($matches[0] as $line) {
                    $body_length += strlen($line);

                    $r = $this->highlightedMarkup($line, $highlight);
                    if ($r) {
                        $body_string[] = $r;
                    }
                    if ($body_length > 150) {
                        break;
                    }
                }
                if (!empty($body_string)) {
                    $result = (string) @implode('&hellip;<wbr>', $body_string);
                }
            }
        }

        return $result;
    }

    /**
     * Set the color to be used to highlight results.
     *
     * @param string $color
     */
    public function setHighlightColor($color)
    {
        $this->hColor = $color;
    }

    /**
     * Is there some page in the PageSearchIndex database table?
     *
     * @return bool
     */
    public function indexExists()
    {
        $db = Database::connection();

        return (bool) $db->fetchColumn('select cID from PageSearchIndex limit 1');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::cacheBlockOutput()
     */
    public function cacheBlockOutput()
    {
        if ($this->btCacheBlockOutput === null) {
            $this->btCacheBlockOutput = false;
            if ($this->postTo_cID || (string) $this->resultsURL !== '') {
                if ($this->request->get('query') === null) {
                    $this->btCacheBlockOutput = true;
                }
            }
            $this->btCacheBlockOutput = (($this->postTo_cID || $this->resultsURL) && Request::request('query') === null);
        }

        return $this->btCacheBlockOutput;
    }

    /**
     * Default view method.
     */
    public function view()
    {
        $this->set('title', $this->title);
        $this->set('buttonText', $this->buttonText);
        $this->set('baseSearchPath', $this->baseSearchPath);
        $this->set('postTo_cID', $this->postTo_cID);

        if ((string) $this->resultsURL !== '') {
            $resultsPage = null;
            $resultsURL = $this->resultsURL;
        } else {
            $resultsPage = $this->postTo_cID ? Page::getById($this->postTo_cID) : null;
            if (is_object($resultsPage) && !$resultsPage->isError()) {
                $resultsURL = $resultsPage->getCollectionPath();
            } else {
                $resultsPage = null;
                $c = Page::getCurrentPage();
                $resultsURL = $c->getCollectionPath();
            }
        }

        $resultsURL = $this->app->make('helper/text')->encodePath($resultsURL);

        $this->set('resultTargetURL', $resultsURL);
        if ($resultsPage !== null) {
            $this->set('resultTarget', $resultsPage);
        } else {
            $this->set('resultTarget', $resultsURL);
        }

        //run query if display results elsewhere not set, or the cID of this page is set
        if ($resultsPage === null && (string) $this->resultsURL === '') {
            if ((string) $this->request->request('query') !== '' || $this->request->request('akID') || $this->request->request('month')) {
                $this->do_search();
            }
        }
    }

    /**
     * Method called when the "add block" dialog is going to be shown.
     */
    public function add()
    {
        $this->edit();
    }

    /**
     * Method called when the "edit block" dialog is going to be shown.
     */
    public function edit()
    {
        $this->set('pageSelector', $this->app->make('helper/form/page_selector'));
    }

    /**
     * {@inheritdoc}
     */
    public function save($data)
    {
        $data += [
            'title' => '',
            'buttonText' => '',
            'baseSearchPath' => '',
            'searchUnderCID' => 0,
            'postTo_cID' => 0,
            'externalTarget' => 0,
            'resultsURL' => '',
            'resultsPageKind' => '',
        ];
        $args = [
            'title' => (string) $data['title'],
            'buttonText' => (string) $data['buttonText'],
            'baseSearchPath' => '',
            'postTo_cID' => null,
            'resultsURL' => '',
        ];
        switch ($data['baseSearchPath']) {
            case 'THIS':
                $c = Page::getCurrentPage();
                if (is_object($c) && !$c->isError()) {
                    $args['baseSearchPath'] = (string) $c->getCollectionPath();
                }
                break;
            case 'OTHER':
                if ($data['searchUnderCID']) {
                    $searchUnderCID = (int) $data['searchUnderCID'];
                    $searchUnderC = Page::getByID($searchUnderCID);
                    if (is_object($searchUnderC) && !$searchUnderC->isError()) {
                        $args['baseSearchPath'] = (string) $searchUnderC->getCollectionPath();
                    }
                }
                break;
        }
        if ($args['baseSearchPath'] === '/') {
            $args['baseSearchPath'] = '';
        }
        
        switch ($data['resultsPageKind']) {
            case 'CID':
                if ($data['postTo_cID']) {
                    $postTo_cID = (int) $data['postTo_cID'];
                    $postTo_c = Page::getByID($postTo_cID);
                    if (is_object($postTo_c) && !$postTo_c->isError()) {
                        $args['postTo_cID'] = $postTo_cID;
                    }
                }
                break;
            case 'URL':
                $args['resultsURL'] = (string) $data['resultsURL'];
                break;
        }
        parent::save($args);
    }

    /**
     * Perform the search.
     *
     * @return null|false
     */
    public function do_search()
    {
        $query = (string) $this->request->request('query');

        $ipl = new PageList();
        $aksearch = false;
        $akIDs = $this->request->request('akID');
        if (is_array($akIDs)) {
            foreach ($akIDs as $akID => $req) {
                $fak = CollectionAttributeKey::getByID($akID);
                if (is_object($fak)) {
                    $type = $fak->getAttributeType();
                    $cnt = $type->getController();
                    $cnt->setAttributeKey($fak);
                    $cnt->searchForm($ipl);
                    $aksearch = true;
                }
            }
        }

        if ($this->request->request('month') !== null && $this->request->request('year') !== null) {
            $year = @(int) ($this->request->request('year'));
            $month = abs(@(int) ($this->request->request('month')));
            if (strlen(abs($year)) < 4) {
                $year = (($year < 0) ? '-' : '') . str_pad(abs($year), 4, '0', STR_PAD_LEFT);
            }
            if ($month < 12) {
                $month = str_pad($month, 2, '0', STR_PAD_LEFT);
            }
            $daysInMonth = date('t', strtotime("$year-$month-01"));
            $dh = Core::make('helper/date');
            /* @var $dh \Concrete\Core\Localization\Service\Date */
            $start = $dh->toDB("$year-$month-01 00:00:00", 'user');
            $end = $dh->toDB("$year-$month-$daysInMonth 23:59:59", 'user');
            $ipl->filterByPublicDate($start, '>=');
            $ipl->filterByPublicDate($end, '<=');
            $aksearch = true;
        }

        if ($query === '' && $aksearch === false) {
            return false;
        }

        if ($query !== '') {
            $ipl->filterByKeywords($query);
        }

        $search_paths = $this->request->request('search_paths');
        if (is_array($search_paths)) {
            foreach ($search_paths as $path) {
                if ($path === '') {
                    continue;
                }
                $ipl->filterByPath($path);
            }
        } elseif ($this->baseSearchPath != '') {
            $ipl->filterByPath($this->baseSearchPath);
        }

        $cak = CollectionKey::getByHandle('exclude_search_index');
        if (is_object($cak)) {
            $ipl->filterByExcludeSearchIndex(false);
        }

        $pagination = $ipl->getPagination();
        $results = $pagination->getCurrentPageResults();

        $this->set('query', $query);
        $this->set('results', $results);
        $this->set('do_search', true);
        $this->set('searchList', $ipl);
        $this->set('pagination', $pagination);
    }
}
