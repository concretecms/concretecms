<?php

namespace Concrete\Block\ImageSlider;

use Concrete\Core\Editor\LinkAbstractor;
use Concrete\Core\Block\ItemListBlockController;
use Concrete\Core\File\Tracker\FileTrackableInterface;
use Concrete\Core\Statistics\UsageTracker\AggregateTracker;
use Page;

class Controller extends ItemListBlockController implements FileTrackableInterface
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

    protected function getItemListTable()
    {
        return 'btImageSliderEntries';
    }

    public function getSearchableContent()
    {
        $content = '';

        $rows = $this->getItems();
        foreach ($rows as $row) {
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
        $this->set('rows', $this->getItems(['sortOrder' => 'ASC']));
    }

    public function composer()
    {
        $this->edit();
    }

    public function registerViewAssets($outputContent = '')
    {
        $this->requireAsset('javascript', 'jquery');
        $this->requireAsset('responsive-slides');
    }

    public function getEntries()
    {
        $r = $this->getItems(['sortOrder' => 'ASC']);
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

    public function delete()
    {
        parent::delete();
        $this->getTracker()->forget($this);
    }

    public function validate($args)
    {
        $error = $this->app->make('helper/validation/error');
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

        if (isset($args['sortOrder'])) {
            $count = count($args['sortOrder']);
            $i = 0;

            while ($i < $count) {
                switch ((int)$args['linkType'][$i]) {
                    case 1:
                        $args['linkURL'][$i] = '';
                        break;
                    case 2:
                        $args['internalLinkCID'][$i] = 0;
                        break;
                    default:
                        $args['linkURL'][$i] = '';
                        $args['internalLinkCID'][$i] = 0;
                        break;
                }
            }
        }

        parent::save($args);

        $this->getTracker()->track($this);
    }

    /**
     * In which case the slide entry is valid
     *
     * @param array $item
     * @return bool
     */
    protected function isValidItem(array $item)
    {
        // Skip item if fID, title and description are empty
        $description = trim(strip_tags($item['description']));
        return $item['fID'] > 0 || trim($item['title']) != '' || !empty($description);
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
