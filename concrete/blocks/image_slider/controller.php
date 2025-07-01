<?php

namespace Concrete\Block\ImageSlider;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Editor\LinkAbstractor;
use Concrete\Core\Feature\Features;
use Concrete\Core\File\Tracker\FileTrackableInterface;
use Concrete\Core\File\Tracker\RichTextExtractor;
use Concrete\Core\Feature\UsesFeatureInterface;
use Concrete\Core\Statistics\UsageTracker\AggregateTracker;
use Core;
use Database;
use Page;

class Controller extends BlockController implements FileTrackableInterface, UsesFeatureInterface
{
    /**
     * @var int|string|null
     */
    public $navigationType;

    /**
     * @var int|string|null
     */
    public $timeout;

    /**
     * @var int|string|null
     */
    public $speed;

    /**
     * @var int|string|null
     */
    public $noAnimate;

    /**
     * @var int|string|null
     */
    public $pause;

    /**
     * @var int|string|null
     */
    public $maxWidth;

    protected $btTable = 'btImageSlider';
    protected $btExportTables = ['btImageSlider', 'btImageSliderEntries'];
    protected $btInterfaceWidth = 600;
    protected $btInterfaceHeight = 550;
    protected $btWrapperClass = 'ccm-ui';
    protected $btCacheBlockRecord = true;
    protected $btExportFileColumns = ['fID'];
    protected $btExportContentColumns = ['title', 'description'];
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
    public function __construct($obj = null, ?AggregateTracker $tracker = null)
    {
        parent::__construct($obj);
        $this->tracker = $tracker;
    }

    public function getRequiredFeatures(): array
    {
        return [
            Features::IMAGERY
        ];
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
        foreach ($this->getRawEntries() as $entry) {
            $content .= "{$entry['title']} {$entry['description']} ";
        }

        return $content;
    }

    public function edit()
    {
        $entries = array_map(
            static function (array $row): array {
                $row['description'] = LinkAbstractor::translateFromEditMode($row['description']);

                return $row;
            },
            $this->getRawEntries()
        );
        $this->set('rows', $entries);
    }

    public function add()
    {
        $this->set('navigationType', 0);
        $this->set('timeout', null);
        $this->set('speed', null);
        $this->set('noAnimate', false);
        $this->set('pause', false);
        $this->set('maxWidth', null);
        $this->set('maxHeight', null);
        $this->set('rows', []);
    }

    public function composer()
    {
        $this->edit();
    }

    public function getEntries()
    {
        return array_map(
            static function (array $row): array {
                if (!$row['linkURL'] && $row['internalLinkCID']) {
                    $c = Page::getByID($row['internalLinkCID'], 'ACTIVE');
                    $row['linkURL'] = $c->getCollectionLink();
                    $row['linkPage'] = $c;
                }
                $row['description'] = LinkAbstractor::translateFrom($row['description']);

                return $row;
            },
            $this->getRawEntries()
        );
    }

    public function view()
    {
        $this->set('rows', $this->getEntries());
    }

    public function duplicate($newBID)
    {
        parent::duplicate($newBID);
        $db = $this->app->make(Connection::class);
        $copyFields = 'fID, linkURL, title, description, sortOrder, internalLinkCID';
        $db->executeUpdate(
            "INSERT INTO btImageSliderEntries (bID, {$copyFields}) SELECT ?, {$copyFields} FROM btImageSliderEntries WHERE bID = ?",
            [
                $newBID,
                $this->bID
            ]
        );
    }

    public function delete()
    {
        $db = Database::get();
        $db->delete('btImageSliderEntries', ['bID' => $this->bID]);
        parent::delete();
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
        $args['noAnimate'] = empty($args['noAnimate']) ? 0 : 1;
        $args['pause'] = empty($args['pause']) ? 0 : 1;
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
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\File\Tracker\FileTrackableInterface::getUsedFiles()
     */
    public function getUsedFiles()
    {
        $result = [];
        $richTextExtractor = $this->app->make(RichTextExtractor::class);
        foreach ($this->getRawEntries() as $entry) {
            $result = array_merge($result, $richTextExtractor->extractFiles($entry['description']));
            if (($fID = (int) $entry['fID']) > 0) {
                $result[] = $fID;
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::export()
     */
    public function export(\SimpleXMLElement $blockNode)
    {
        parent::export($blockNode);
        $nodesToRemove = $blockNode->xpath('./data[@table="btImageSliderEntries"]/record/id');
        if ($nodesToRemove) {
            foreach ($nodesToRemove as $nodeToRemove) {
                unset($nodeToRemove[0]);
            }
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::importAdditionalData()
     */
    protected function importAdditionalData($b, $blockNode)
    {
        $nodesToRemove = $blockNode->xpath('./data[@table="btImageSliderEntries"]/record/id');
        if ($nodesToRemove) {
            foreach ($nodesToRemove as $nodeToRemove) {
                unset($nodeToRemove[0]);
            }
        }
        parent::importAdditionalData($b, $blockNode);
    }

    private function getRawEntries(): array
    {
        $db = $this->app->make(Connection::class);

        return $db->fetchAllAssociative(
            'SELECT * FROM btImageSliderEntries WHERE bID = ? ORDER BY sortOrder',
            [$this->bID]
        );
    }
}
