<?php

namespace Concrete\Block\ImageSlider;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Editor\LinkAbstractor;
use Concrete\Core\File\Tracker\FileTrackableInterface;
use Concrete\Core\Statistics\UsageTracker\AggregateTracker;
use Core;
use Database;
use Page;

class Controller extends BlockController implements FileTrackableInterface
{
    protected $btTable = 'btImageSlider';
    protected $btExportTables = ['btImageSlider', 'btImageSliderEntries'];
    protected $btInterfaceWidth = 600;
    protected $btInterfaceHeight = 550;
    protected $btWrapperClass = 'ccm-ui';
    protected $btCacheBlockRecord = true;
    protected $btExportFileColumns = ['fID'];
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = true;
    protected $btCacheBlockOutputForRegisteredUsers = false;
    protected $btIgnorePageThemeGridFrameworkContainer = true;

    /**
     * @var \Concrete\Core\Statistics\UsageTracker\AggregateTracker|null
     */
    protected $tracker;

    /**
     * Instantiates the block controller.
     *
     * @param \Concrete\Core\Block\BlockType\BlockType|null $obj
     * @param \Concrete\Core\Statistics\UsageTracker\AggregateTracker|null $tracker
     */
    public function __construct($obj = null, AggregateTracker $tracker = null)
    {
        parent::__construct($obj);
        $this->tracker = $tracker;
    }

    public function getBlockTypeDescription()
    {
        return t('Display your images and captions in an attractive slideshow format.');
    }

    public function getBlockTypeName()
    {
        return t('Image Slider');
    }

    public function getSearchableContent()
    {
        $content = '';
        $db = Database::get();
        $v = [$this->bID];
        $q = 'select * from btImageSliderEntries where bID = ?';
        $r = $db->query($q, $v);
        foreach ($r as $row) {
            $content .= $row['title'] . ' ';
            $content .= $row['description'] . ' ';
        }

        return $content;
    }

    public function add()
    {
        $this->requireAsset('core/file-manager');
        $this->requireAsset('core/sitemap');
    }

    public function edit()
    {
        $this->requireAsset('core/file-manager');
        $this->requireAsset('core/sitemap');
        $db = Database::get();
        $query = $db->GetAll('SELECT * from btImageSliderEntries WHERE bID = ? ORDER BY sortOrder', [$this->bID]);
        $this->set('rows', $query);
    }

    public function composer()
    {
        $this->edit();
    }

    public function registerViewAssets($outputContent = '')
    {
        $al = \Concrete\Core\Asset\AssetList::getInstance();

        $this->requireAsset('javascript', 'jquery');
        $this->requireAsset('responsive-slides');

        $al->register('javascript', 'responsiveslides', 'blocks/image_slider/responsiveslides.js');
        $this->requireAsset('javascript', 'blocks/image_slider/responsiveslides');

        $al->register('css', 'responsiveslides', 'blocks/image_slider/responsiveslides.css');
        $this->requireAsset('css', 'blocks/image_slider/responsiveslides');
    }

    public function getEntries()
    {
        $db = Database::get();
        $r = $db->GetAll('SELECT * from btImageSliderEntries WHERE bID = ? ORDER BY sortOrder', [$this->bID]);
        // in view mode, linkURL takes us to where we need to go whether it's on our site or elsewhere
        $rows = [];
        foreach ($r as $q) {
            if (!$q['linkURL'] && $q['internalLinkCID']) {
                $c = Page::getByID($q['internalLinkCID'], 'ACTIVE');
                $q['linkURL'] = $c->getCollectionLink();
                $q['linkPage'] = $c;
            }
            $q['description'] = LinkAbstractor::translateFrom($q['description']);
            $rows[] = $q;
        }

        return $rows;
    }

    public function view()
    {
        $this->set('rows', $this->getEntries());
    }

    public function duplicate($newBID)
    {
        parent::duplicate($newBID);
        $db = Database::get();
        $v = [$this->bID];
        $q = 'select * from btImageSliderEntries where bID = ?';
        $r = $db->query($q, $v);
        while ($row = $r->FetchRow()) {
            $db->execute('INSERT INTO btImageSliderEntries (bID, fID, linkURL, title, description, sortOrder, internalLinkCID) values(?,?,?,?,?,?,?)',
                [
                    $newBID,
                    $row['fID'],
                    $row['linkURL'],
                    $row['title'],
                    $row['description'],
                    $row['sortOrder'],
                    $row['internalLinkCID'],
                ]
            );
        }
    }

    public function delete()
    {
        $db = Database::get();
        $db->delete('btImageSliderEntries', ['bID' => $this->bID]);
        parent::delete();
        $this->getTracker()->forget($this);
    }

    public function validate($args)
    {
        $error = Core::make('helper/validation/error');
        $timeout = (int) $args['timeout'];
        $speed = (int) $args['speed'];

        if (!$timeout) {
            $error->add(t('Slide Duration must be greater than 0.'));
        }
        if (!$speed) {
            $error->add(t('Slide Transition Speed must be greater than 0.'));
        }
        // https://github.com/viljamis/ResponsiveSlides.js/issues/132#issuecomment-12543345
        // "The 'timeout' (amount of time spent on one slide) has to be at least 100 bigger than 'speed', otherwise the function simply returns."
        if (($timeout - $speed) < 100) {
            $error->add(t('Slide Duration must be at least 100 ms greater than the Slide Transition Speed.'));
        }

        return $error;
    }

    public function save($args)
    {
        $args += [
            'timeout' => 4000,
            'speed' => 500,
        ];
        $args['timeout'] = (int) $args['timeout'];
        $args['speed'] = (int) $args['speed'];
        $args['noAnimate'] = isset($args['noAnimate']) ? 1 : 0;
        $args['pause'] = isset($args['pause']) ? 1 : 0;
        $args['maxWidth'] = isset($args['maxWidth']) ? (int) $args['maxWidth'] : 0;

        $db = Database::get();
        $db->execute('DELETE from btImageSliderEntries WHERE bID = ?', [$this->bID]);
        parent::save($args);
        if (isset($args['sortOrder'])) {
            $count = count($args['sortOrder']);
            $i = 0;

            while ($i < $count) {
                $linkURL = $args['linkURL'][$i];
                $internalLinkCID = $args['internalLinkCID'][$i];
                switch ((int) $args['linkType'][$i]) {
                    case 1:
                        $linkURL = '';
                        break;
                    case 2:
                        $internalLinkCID = 0;
                        break;
                    default:
                        $linkURL = '';
                        $internalLinkCID = 0;
                        break;
                }

                if (isset($args['description'][$i])) {
                    $args['description'][$i] = LinkAbstractor::translateTo($args['description'][$i]);
                }

                $db->execute('INSERT INTO btImageSliderEntries (bID, fID, title, description, sortOrder, linkURL, internalLinkCID) values(?, ?, ?, ?,?,?,?)',
                    [
                        $this->bID,
                        (int) $args['fID'][$i],
                        $args['title'][$i],
                        $args['description'][$i],
                        $args['sortOrder'][$i],
                        $linkURL,
                        $internalLinkCID,
                    ]
                );
                ++$i;
            }
        }
        $this->getTracker()->track($this);
    }

    public function getUsedFiles()
    {
        return array_map(function ($entry) {
            return $entry['fID'];
        }, $this->getEntries());
    }

    public function getUsedCollection()
    {
        return $this->getCollectionObject();
    }

    /**
     * @return \Concrete\Core\Statistics\UsageTracker\AggregateTracker
     */
    protected function getTracker()
    {
        if ($this->tracker === null) {
            $this->tracker = $this->app->make(AggregateTracker::class);
        }

        return $this->tracker;
    }
}
