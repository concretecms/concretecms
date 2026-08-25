<?php
namespace Concrete\Block\RssDisplayer;

use Concrete\Core\Api\ApiValueSchemaInterface;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Feature\Features;
use Concrete\Core\Feature\UsesFeatureInterface;
use Loader;
use Core;

class Controller extends BlockController implements ApiValueSchemaInterface, UsesFeatureInterface
{
    /**
     * @var string|null
     */
    public $title = '';

    /**
     * @var string|null
     */
    public $url;

    /**
     * @var string|null
     */
    public $dateFormat;

    /**
     * @var int|string|null
     */
    public $itemsToDisplay = 5;

    /**
     * @var bool|int|string|null
     */
    public $showSummary = 1;

    /**
     * @var bool|int|string|null
     */
    public $launchInNewWindow = 1;

    /**
     * @var string|null
     */
    public $titleFormat;

    protected $btTable = 'btRssDisplay';
    protected $btInterfaceWidth = 400;
    protected $btInterfaceHeight = 550;
    protected $btCacheBlockRecord = true;
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = true;
    protected $btWrapperClass = 'ccm-ui';
    protected $btCacheBlockOutputForRegisteredUsers = true;
    protected $btCacheBlockOutputOnEditMode = true;

    /**
     * Default number of seconds that the output of this block should be cached
     * (Can be overridden by the user within C5 UI).
     *
     * @var int
     */
    protected $btCacheBlockOutputLifetime = 3600;

    /**
     * Number of seconds that the RSS feed itself should be cached before fetching
     * a fresh copy.
     *
     * (Perhaps this could eventually become a user-setting?)
     *
     * Caching is important as fetching a remote URL can significantly delay
     * the rendering of a PHP page.
     *
     * Setting to "null" should cache it indefinitely until cache is manually cleared.
     *
     * Should probably be less than $btCacheBlockOutputLifetime above, otherwise the
     * block will be re-rendered using the same stale RSS data.
     *
     * @var int
     */
    protected $rssFeedCacheLifetime = 1800;

    public function getRequiredFeatures(): array
    {
        return [
            Features::NAVIGATION
        ];
    }

    /**
     * Used for localization. If we want to localize the name/description we have to include this.
     */
    public function getBlockTypeDescription()
    {
        return t("Fetch, parse and display the contents of an RSS or Atom feed.");
    }

    public function getBlockTypeName()
    {
        return t("RSS Displayer");
    }

    public function getDefaultDateTimeFormats()
    {
        $formats = [
            ':longDate:',
            ':shortDate:',
            ':longTime:',
            ':shortTime:',
            ':longDate:longTime:',
            ':longDate:shortTime:',
            ':shortDate:longTime:',
            ':shortDate:shortTime:',
        ];
        $now = new \DateTime();
        $result = [];
        foreach ($formats as $format) {
            $result[$format] = $this->formatDateTime($now, $format);
        }

        return $result;
    }

    public function add()
    {
        $this->set('url', '');
        $this->set('title', '');
        $this->set('titleFormat', 'h5');
        $this->set('dateFormat', ':longDate:shortTime:');
        $this->set('itemsToDisplay', '5');
        $this->set('showSummary', true);
        $this->set('launchInNewWindow', true);
    }
    /**
     * Format a \DateTime instance accordingly to $format.
     *
     * @param \DateTime|null $date
     * @param string|bool $format Set to true (default) to use the default format
     */
    public function formatDateTime($date, $format = true)
    {
        $result = '';
        if (is_a($date, '\\DateTime')) {
            if ($format === true) {
                $format = $this->dateFormat;
                if (!$format) {
                    $formats = $this->getDefaultDateTimeFormats();
                    reset($formats);
                    $format = key($formats);
                }
            }
            $dh = Core::make('helper/date');
            /* @var $dh \Concrete\Core\Localization\Service\Date */
            switch ($format) {
                case ':shortDate:shortTime:':
                    $result = $dh->formatDateTime($date, false, false);
                    break;
                case ':shortDate:longTime:':
                    $result = $dh->formatDateTime($date, false, true);
                    break;
                case ':longDate:shortTime:':
                    $result = $dh->formatDateTime($date, true, false);
                    break;
                case ':longDate:longTime:':
                    $result = $dh->formatDateTime($date, true, true);
                    break;
                case ':shortDate:':
                    $result = $dh->formatDate($date, false);
                    break;
                case ':longDate:':
                    $result = $dh->formatDate($date, true);
                    break;
                case ':shortTime:':
                    $result = $dh->formatTime($date, false);
                    break;
                case ':longTime:':
                    $result = $dh->formatTime($date, true);
                    break;
                default:
                    $result = $dh->formatCustom($format, $date);
            }
        }

        return $result;
    }

    public function view()
    {
        $fp = Loader::helper("feed");
        $posts = [];

        try {
            $channel = $fp->load($this->url, $this->rssFeedCacheLifetime);
            $posts = array_slice($fp->getPosts($channel), 0, intval($this->itemsToDisplay));
        } catch (\Exception $e) {
            $this->set('errorMsg', t('Unable to load RSS posts.'));
        }

        if (empty($this->titleFormat)) {
            $this->set('titleFormat', 'h3');
        }
        $this->set('posts', $posts);
        $this->set('title', $this->title);
    }

    public function save($data)
    {
        $data += [
            'url' => '',
            'itemsToDisplay' => 0,
            'showSummary' => 0,
            'launchInNewWindow' => 0,
            'title' => '',
            'standardDateFormat' => '',
            'customDateFormat' => '',
            'titleFormat' => '',
        ];
        $args = [
            'url' => $data['url'],
            'itemsToDisplay' => (intval($data['itemsToDisplay']) > 0) ? intval($data['itemsToDisplay']) : 5,
            'showSummary' => $data['showSummary'] ? 1 : 0,
            'launchInNewWindow' => $data['launchInNewWindow'] ? 1 : 0,
            'title' => $data['title'],
            'titleFormat' => $data['titleFormat'],
        ];
        switch ($data['standardDateFormat']) {
            case ':custom:':
                $args['dateFormat'] = $data['customDateFormat'];
                break;
            default:
                $args['dateFormat'] = $data['standardDateFormat'];
                break;
        }
        parent::save($args);
    }

    public function getSearchableContent()
    {
        $fp = Loader::helper("feed");
        $posts = [];

        try {
            // We manually set cache time to 2hrs here as getSearchableContent()
            // can probably cope with slightly older data
            $channel = $fp->load($this->url, 7200);
            $posts = array_slice($fp->getPosts($channel), 0, intval($this->itemsToDisplay));
        } catch (\Exception $e) {
        }

        $searchContent = '';
        foreach ($posts as $item) {
            $searchContent .= $item->getTitle() . ' ' . strip_tags($item->getDescription()) . ' ';
        }

        return $searchContent;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Api\ApiValueSchemaInterface::getApiValueSchema()
     */
    public function getApiValueSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'url' => [
                    'type' => ['string', 'null'],
                    'maxLength' => 255,
                    'description' => 'The address of the RSS or Atom feed to be displayed.',
                ],
                'title' => [
                    'type' => ['string', 'null'],
                    'maxLength' => 255,
                    'description' => 'The text displayed above the posts.',
                ],
                'titleFormat' => [
                    'type' => 'string',
                    'enum' => array_keys(BlockController::$btTitleFormats),
                    'default' => 'h5',
                    'description' => 'The HTML element wrapping the text displayed above the posts.',
                ],
                'itemsToDisplay' => [
                    'type' => ['string', 'integer', 'null'],
                    'default' => '5',
                    'description' => 'The number of posts to be displayed.',
                ],
                'showSummary' => [
                    'type' => ['string', 'integer'],
                    'description' => 'Set it to 1 to display the summary of every post, and not just its title.',
                ],
                'launchInNewWindow' => [
                    'type' => ['string', 'integer'],
                    'description' => 'Set it to 1 to open the posts in another window.',
                ],
                'dateFormat' => [
                    'type' => ['string', 'null'],
                    'maxLength' => 100,
                    'description' => 'How the date of a post is displayed: one of the formats of the site (' . implode(', ', array_keys($this->getDefaultDateTimeFormats())) . '), or a format accepted by the date() PHP function.',
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::getImportData()
     */
    protected function getImportData($blockNode, $page)
    {
        $data = parent::getImportData($blockNode, $page);
        $dateFormat = $data['dateFormat'] ?? '';
        if (array_key_exists($dateFormat, $this->getDefaultDateTimeFormats())) {
            $data['standardDateFormat'] = $dateFormat;
        } else {
            $data['standardDateFormat'] = ':custom:';
            $data['customDateFormat'] = $dateFormat;
        }

        return $data;
    }
}
