<?php
namespace Concrete\Block\RssDisplayer;

use Concrete\Core\Block\BlockController;
use Loader;
use Core;

class Controller extends BlockController
{

    public $itemsToDisplay = "5";
    public $showSummary = "1";
    public $launchInNewWindow = "1";
    public $title = "";
    protected $btTable = 'btRssDisplay';
    protected $btInterfaceWidth = 400;
    protected $btInterfaceHeight = 550;
    protected $btCacheBlockRecord = true;
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = true;
    protected $btWrapperClass = 'ccm-ui';
    protected $btCacheBlockOutputForRegisteredUsers = true;
    
    /**
     * Default number of seconds that the output of this block should be cached
     * (Can be overridden by the user within C5 UI)
     * 
     * @var integer
     */
    protected $btCacheBlockOutputLifetime = 3600;
    
    /**
     * Number of seconds that the RSS feed itself should be cached before fetching
     * a fresh copy 
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
     * @var integer
     */
    protected $rssFeedCacheLifetime = 1800;   

    /**
     * Used for localization. If we want to localize the name/description we have to include this
     */
    public function getBlockTypeDescription()
    {
        return t("Fetch, parse and display the contents of an RSS or Atom feed.");
    }

    public function getBlockTypeName()
    {
        return t("RSS Displayer");
    }

    public function getJavaScriptStrings()
    {
        return array(
            'feed-address'   => t('Please enter a valid feed address.'),
            'feed-num-items' => t('Please enter the number of items to display.')
        );
    }

    public function getDefaultDateTimeFormats()
    {
        $formats = array(
            ':longDate:',
            ':shortDate:',
            ':longTime:',
            ':shortTime:',
            ':longDate:longTime:',
            ':longDate:shortTime:',
            ':shortDate:longTime:',
            ':shortDate:shortTime:'
        );
        $now = new \DateTime();
        $result = array();
        foreach ($formats as $format) {
            $result[$format] = $this->formatDateTime($now, $format);
        }

        return $result;
    }

    /**
     * Format a \DateTime instance accordingly to $format
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
        $posts = array();

        try {
            $channel = $fp->load($this->url, $this->rssFeedCacheLifetime);
            $i = 0;
            foreach ($channel as $post) {
                $posts[] = $post;
                if (($i + 1) == intval($this->itemsToDisplay)) {
                    break;
                }
                $i++;
            }
        } catch (\Exception $e) {
            $this->set('errorMsg', $e->getMessage());
        }

        $this->set('posts', $posts);
        $this->set('title', $this->title);
    }

    public function save($data)
    {
        $args['url'] = isset($data['url']) ? $data['url'] : '';
        $args['dateFormat'] = $data['dateFormat'];
        $args['itemsToDisplay'] = (intval($data['itemsToDisplay']) > 0) ? intval($data['itemsToDisplay']) : 5;
        $args['showSummary'] = ($data['showSummary'] == 1) ? 1 : 0;
        $args['launchInNewWindow'] = ($data['launchInNewWindow'] == 1) ? 1 : 0;
        $args['title'] = isset($data['title']) ? $data['title'] : '';
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
        $posts = array();

        try {
            // We manually set cache time to 2hrs here as getSearchableContent()
            // can probably cope with slightly older data
            $channel = $fp->load($this->url, 7200);
            $i = 0;
            foreach ($channel as $post) {
                $posts[] = $post;
                if (($i + 1) == intval($this->itemsToDisplay)) {
                    break;
                }
                $i++;
            }
        } catch (\Exception $e) {

        }

        $searchContent = '';
        foreach ($posts as $item) {
            $searchContent .= $item->getTitle() . ' ' . strip_tags($item->getDescription()) . ' ';
        }

        return $searchContent;
    }

}
