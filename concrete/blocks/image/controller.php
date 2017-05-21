<?php
namespace Concrete\Block\Image;

use Concrete\Core\Block\Block;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Error\Error;
use Concrete\Core\File\File;
use Concrete\Core\File\Tracker\FileTrackableInterface;
use Concrete\Core\Page\Page;
use Concrete\Core\Statistics\UsageTracker\AggregateTracker;

class Controller extends BlockController implements FileTrackableInterface
{
    protected $btInterfaceWidth = 400;
    protected $btInterfaceHeight = 550;
    protected $btTable = 'btContentImage';
    protected $btCacheBlockRecord = true;
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = true;
    protected $btCacheBlockOutputForRegisteredUsers = true;
    protected $btWrapperClass = 'ccm-ui';
    protected $btExportFileColumns = ['fID', 'fOnstateID', 'fileLinkID'];
    protected $btExportPageColumns = ['internalLinkCID'];
    protected $btFeatures = [
        'image',
    ];

    /** @var AggregateTracker */
    protected $tracker;

    public function __construct($blockType = null, AggregateTracker $tracker = null)
    {
        parent::__construct($blockType);
        $this->tracker = $tracker;
    }

    public function getBlockTypeName()
    {
        return t('Image');
    }

    public function getBlockTypeDescription()
    {
        return t('Adds images and onstates from the library to pages.');
    }

    /**
     * @param string $outputContent
     */
    public function registerViewAssets($outputContent = '')
    {
        // Ensure we have jQuery if we have an onState image
        if (is_object($this->getFileOnstateObject())) {
            $this->requireAsset('javascript', 'jquery');
        }
    }

    /**
     * @return bool|null
     */
    public function view()
    {
        // Check for a valid File in the view
        $f = $this->getFileObject();
        $this->set('f', $f);

        // On-State image available
        $foS = $this->getFileOnstateObject();
        $this->set('foS', $foS);

        $imgPaths = [];

        if (is_object($f) && is_object($foS)) {
            if (!$f->getTypeObject()->isSVG() && !$foS->getTypeObject()->isSVG()) {
                if ($this->cropImage && ($this->maxWidth > 0 && $this->maxHeight > 0)) {
                    $im = $this->app->make('helper/image');

                    $fIDThumb = $im->getThumbnail($f, $this->maxWidth, $this->maxHeight, true);
                    $imgPaths['default'] = $fIDThumb->src;

                    $fOnstateThumb = $im->getThumbnail($foS, $this->maxWidth, $this->maxHeight, true);
                    $imgPaths['hover'] = $fOnstateThumb->src;
                } else {
                    $imgPaths['default'] = File::getRelativePathFromID($this->getFileID());
                    $imgPaths['hover'] = File::getRelativePathFromID($this->fOnstateID);
                }
            }
        }

        $this->set('imgPaths', $imgPaths);
        $this->set('altText', $this->getAltText());
        $this->set('title', $this->getTitle());
        $this->set('linkURL', $this->getLinkURL());
        $this->set('c', Page::getCurrentPage());
    }

    public function add()
    {
        $this->set('bf', null);
        $this->set('bfo', null);
        $this->set('linkFile', null);
        $this->set('constrainImage', false);
    }

    public function edit()
    {
        // Image file object
        $bf = null;
        if ($this->getFileID() > 0) {
            $bf = $this->getFileObject();
        }
        $this->set('bf', $bf);

        // Image On-State file object
        $bfo = null;
        if ($this->getFileOnstateID() > 0) {
            $bfo = $this->getFileOnstateObject();
        }
        $this->set('bfo', $bfo);

        // Image Link - File object
        $linkFile = null;
        if ($this->getFileLinkID() > 0) {
            $linkFile = $this->getFileLinkObject();
        }
        $this->set('linkFile', $linkFile);

        // Constrain dimensions
        $constrainImage = $this->maxWidth > 0 || $this->maxHeight > 0;
        $this->set('constrainImage', $constrainImage);

        // Max width is saved as an integer
        if ($this->maxWidth == 0) {
            $this->set('maxWidth', '');
        }

        // Max height is saved as an integer
        if ($this->maxHeight == 0) {
            $this->set('maxHeight', '');
        }

        // None, Internal, or External
        $linkType = 0;
        if (empty($this->externalLink) && !empty($this->internalLinkCID) && empty($this->fileLinkID)) {
            $linkType = 1;
        } elseif (!empty($this->externalLink) && empty($this->internalLinkCID) && empty($this->fileLinkID)) {
            $linkType = 2;
        } elseif (empty($this->externalLink) && empty($this->internalLinkCID) && !empty($this->fileLinkID)) {
            $linkType = 3;
        }
        $this->set('linkType', $linkType);
    }

    /**
     * @return array
     */
    public function getJavaScriptStrings()
    {
        return [
            'image-required' => t('You must select an image.'),
        ];
    }

    /**
     * @return bool
     */
    public function isComposerControlDraftValueEmpty()
    {
        $f = $this->getFileObject();
        if (is_object($f) && $f->getFileID()) {
            return false;
        }

        return true;
    }

    /**
     * @return \Concrete\Core\Entity\File\File|null
     */
    public function getImageFeatureDetailFileObject()
    {
        // i don't know why this->fID isn't sticky in some cases, leading us to query
        // every damn time
        $db = $this->app->make('database')->connection();

        $file = null;
        $fID = $db->fetchColumn('SELECT fID FROM btContentImage WHERE bID = ?', [$this->bID], 0);
        if ($fID) {
            $f = File::getByID($fID);
            if (is_object($f) && $f->getFileID()) {
                $file = $f;
            }
        }

        return $file;
    }

    /**
     * @return int
     */
    public function getFileID()
    {
        return isset($this->record->fID) ? $this->record->fID : (isset($this->fID) ? $this->fID : null);
    }

    /**
     * @return int
     */
    public function getFileOnstateID()
    {
        return $this->fOnstateID;
    }

    /**
     * @return int
     */
    public function getFileLinkID()
    {
        return $this->fileLinkID;
    }

    /**
     * @return \Concrete\Core\Entity\File\File|null
     */
    public function getFileOnstateObject()
    {
        if ($this->fOnstateID) {
            return File::getByID($this->fOnstateID);
        }
    }

    /**
     * @return \Concrete\Core\Entity\File\File|null
     */
    public function getFileObject()
    {
        return File::getByID($this->getFileID());
    }

    /**
     * @return \Concrete\Core\Entity\File\File|null
     */
    public function getFileLinkObject()
    {
        if ($this->fileLinkID) {
            return File::getByID($this->getFileLinkID());
        }
    }

    /**
     * @return string
     */
    public function getAltText()
    {
        return $this->altText;
    }

    /**
     * @return string|null
     */
    public function getTitle()
    {
        return !empty($this->title) ? $this->title : null;
    }

    /**
     * @return string
     */
    public function getExternalLink()
    {
        return $this->externalLink;
    }

    /**
     * @return int
     */
    public function getInternalLinkCID()
    {
        return $this->internalLinkCID;
    }

    /**
     * @return string
     */
    public function getLinkURL()
    {
        $linkUrl = '';
        if (!empty($this->externalLink)) {
            $sec = $this->app->make('helper/security');
            $linkUrl = $sec->sanitizeURL($this->externalLink);
        } elseif (!empty($this->internalLinkCID)) {
            $linkToC = Page::getByID($this->internalLinkCID);
            if (is_object($linkToC) && !$linkToC->isError()) {
                $linkUrl = $linkToC->getCollectionLink();
            }
        } elseif (!empty($this->fileLinkID)) {
            $fileLinkObject = $this->getFileLinkObject();
            if (is_object($fileLinkObject)) {
                $linkUrl = $fileLinkObject->getRelativePath();
            }
        }

        return $linkUrl;
    }

    /**
     * @return Error
     */
    public function validate_composer()
    {
        $e = $this->app->make('helper/validation/error');

        $f = $this->getFileObject();
        if (!is_object($f) || !$f->getFileID()) {
            $e->add(t('You must specify a valid image file.'));
        }

        return $e;
    }

    /**
     * @return Error
     */
    public function validate($args)
    {
        $e = $this->app->make('helper/validation/error');
        $f = File::getByID($args['fID']);
        $svg = false;
        if (is_object($f)) {
            $svg = $f->getTypeObject()->isSVG();
        }

        if (!$args['fID']) {
            $e->add(t('Please select an image.'));
        }

        if (isset($args['cropImage']) && (intval($args['maxWidth']) <= 0 || (intval($args['maxHeight']) <= 0)) && !$svg) {
            $e->add(t('Cropping an image requires setting a max width and max height.'));
        }

        if ($svg && isset($args['constrainImage'])) {
            $e->add(t('SVG images cannot be size constrained.'));
        }

        return $e;
    }

    /**
     * On delete update the tracker.
     */
    public function delete()
    {
        $this->tracker->forget($this);
        parent::delete();
    }

    /**
     * @param array $args
     */
    public function save($args)
    {
        $args = $args + [
            'fID' => 0,
            'fOnstateID' => 0,
            'maxWidth' => 0,
            'maxHeight' => 0,
            'constrainImage' => 0,
            'linkType' => 0,
            'externalLink' => '',
            'internalLinkCID' => 0,
            'fileLinkID' => 0,
        ];

        $args['fID'] = $args['fID'] != '' ? $args['fID'] : 0;
        $args['fOnstateID'] = $args['fOnstateID'] != '' ? $args['fOnstateID'] : 0;
        $args['fileLinkID'] = $args['fileLinkID'] != '' ? $args['fileLinkID'] : 0;
        $args['cropImage'] = isset($args['cropImage']) ? 1 : 0;
        $args['maxWidth'] = intval($args['maxWidth']) > 0 ? intval($args['maxWidth']) : 0;
        $args['maxHeight'] = intval($args['maxHeight']) > 0 ? intval($args['maxHeight']) : 0;

        if (!$args['constrainImage']) {
            $args['cropImage'] = 0;
            $args['maxWidth'] = 0;
            $args['maxHeight'] = 0;
        }

        switch (intval($args['linkType'])) {
            case 1:
                $args['externalLink'] = '';
                $args['fileLinkID'] = 0;
                break;
            case 2:
                $args['internalLinkCID'] = 0;
                $args['fileLinkID'] = 0;
                break;
            case 3:
                $args['externalLink'] = '';
                $args['internalLinkCID'] = 0;
                break;
            default:
                $args['externalLink'] = '';
                $args['internalLinkCID'] = 0;
                $args['fileLinkID'] = 0;
                break;
        }

        // This doesn't get saved to the database. It's only for UI usage.
        unset($args['linkType']);

        parent::save($args);
        $this->tracker->track($this);
    }

    public function getUsedFiles()
    {
        return [$this->getFileID()];
    }

    public function getUsedCollection()
    {
        return $this->getCollectionObject();
    }
}
