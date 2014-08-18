<?php
namespace Concrete\Block\RssDisplayer;

use Concrete\Core\Block\BlockController;
use Loader;

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
    protected $btCacheBlockOutputLifetime = 3600;

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

    public function view()
    {
        $fp = Loader::helper("feed");
        $posts = array();

        try {
            $channel = $fp->load($this->url);
            $i = 0;
            foreach($channel as $post) {
                $posts[] = $post;
                if (($i + 1) == intval($this->itemsToDisplay)) {
                    break;
                }
                $i++;
            }
        } catch(\Exception $e) {
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
        parent::save($args);
    }

    public function getSearchableContent()
    {
        $fp = Loader::helper("feed");

        try {
            $channel = $fp->load($this->url);
            $i = 0;
            foreach($channel as $post) {
                $posts[] = $post;
                if (($i + 1) == intval($this->itemsToDisplay)) {
                    break;
                }
                $i++;
            }
        } catch(\Exception $e) {

        }

        $searchContent = '';
        foreach ($posts as $item) {
            $searchContent .= $item->getTitle() . ' ' . strip_tags($item->getDescription()) . ' ';
        }
        return $searchContent;
    }

}
