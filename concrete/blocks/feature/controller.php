<?php
namespace Concrete\Block\Feature;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Editor\LinkAbstractor;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Feature\Features;
use Concrete\Core\Feature\UsesFeatureInterface;
use Concrete\Core\File\File;
use Concrete\Core\File\Tracker\FileTrackableInterface;
use Concrete\Core\File\Tracker\RichTextExtractor;
use Concrete\Core\Form\Service\DestinationPicker\DestinationPicker;
use Concrete\Core\Html\Service\FontAwesomeIcon;
use Concrete\Core\Page\Page;

class Controller extends BlockController implements FileTrackableInterface, UsesFeatureInterface
{
    /**
     * @var string|null
     */
    protected $icon;

    /**
     * @var string|null
     */
    public $title;

    /**
     * @var string|null
     */
    public $paragraph;

    /**
     * @var string|null
     */
    public $externalLink;

    /**
     * @var int|string|null
     */
    public $internalLinkCID;

    /**
     * @var string|null
     */
    public $titleFormat;

    /**
     * @var int|string|null
     */
    public $fID;

    public $helpers = array('form');

    protected $btInterfaceWidth = 400;
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = true;
    protected $btCacheBlockOutputForRegisteredUsers = true;
    protected $btExportPageColumns = ['internalLinkCID'];
    protected $btExportFileColumns = ['fID'];
    protected $btExportContentColumns = ['paragraph'];
    protected $btInterfaceHeight = 520;
    protected $btTable = 'btFeature';

    public function getBlockTypeDescription()
    {
        return t("Displays an icon, a title, and a short paragraph description.");
    }

    public function getBlockTypeName()
    {
        return t("Feature");
    }

    public function getRequiredFeatures(): array
    {
        return [
            Features::BASICS
        ];
    }

    public function getLinkURL()
    {
        if (!empty($this->externalLink)) {
            return $this->externalLink;
        } else {
            if (!empty($this->internalLinkCID)) {
                $linkToC = Page::getByID($this->internalLinkCID);

                return (empty($linkToC) || $linkToC->error) ? '' : $this->app->make('helper/navigation')->getLinkToCollection(
                    $linkToC
                );
            } else {
                return '';
            }
        }
    }

    public function getParagraph()
    {
        return LinkAbstractor::translateFrom($this->paragraph);
    }

    public function getParagraphEditMode()
    {
        return LinkAbstractor::translateFromEditMode($this->paragraph);
    }

    public function registerViewAssets($outputContent = '')
    {
        $this->requireAsset('css', 'font-awesome');
        if (is_object($this->block) && $this->block->getBlockFilename() == 'hover_description') {
            // this isn't great but it's the only way to do this and still make block
            // output caching available to this block.
            $this->requireAsset('javascript', 'bootstrap/tooltip');
            $this->requireAsset('css', 'bootstrap/tooltip');
        }
    }

    public function add()
    {
        $this->set('titleFormat', 'h4');
        $this->edit();
        $this->set('bf', null);
        $this->set('destinationPicker', $this->app->make(DestinationPicker::class));
        $this->set('linkDestinationPickers', $this->getLinkDestinationPickers());
        $this->set('linkDestinationHandle', 'none');
        $this->set('linkDestinationValue', null);
    }

    public function view()
    {
        $this->set('iconTag', FontAwesomeIcon::getFromClassNames(h($this->icon)));
        $this->set('paragraph', LinkAbstractor::translateFrom($this->paragraph));
        $this->set('linkURL', $this->getLinkURL());

        // Check for a valid File in the view
        $f = $this->getFileObject();
        $this->set('f', $f);
    }

    public function edit()
    {
        // Image file object
        $bf = null;
        if ($this->getFileID() > 0) {
            $bf = $this->getFileObject();
        }
        $this->set('bf', $bf);
        $this->requireAsset('css', 'font-awesome');
        $this->set('destinationPicker', $this->app->make(DestinationPicker::class));
        $this->set('linkDestinationPickers', $this->getLinkDestinationPickers());
        if ($this->internalLinkCID) {
            $this->set('linkDestinationHandle', 'page');
            $this->set('linkDestinationValue', $this->internalLinkCID);
        } elseif ((string) $this->externalLink !== '') {
            $this->set('linkDestinationHandle', 'external_url');
            $this->set('linkDestinationValue', $this->externalLink);
        } else {
            $this->set('linkDestinationHandle', 'none');
            $this->set('linkDestinationValue', null);
        }
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
     * @return int
     */
    public function getFileID()
    {
        return isset($this->record->fID) ? $this->record->fID : (isset($this->fID) ? $this->fID : null);
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

    public function getSearchableContent()
    {
        return $this->title . ' ' . $this->paragraph;
    }

    public function save($args)
    {
        $errors = $this->app->make('error');
        $security = $this->app->make('helper/security');

        $args['icon'] = isset($args['icon']) ? $security->sanitizeString($args['icon']) : '';
        $args['fID'] = empty($args['fID']) ? 0 : (int) $args['fID'];
        $args['title'] = isset($args['title']) ? $security->sanitizeString($args['title']) : '';
        $args['titleFormat'] = isset($args['titleFormat']) ? $security->sanitizeString($args['titleFormat']) : '';
        $args['paragraph'] = isset($args['paragraph']) ? LinkAbstractor::translateTo($args['paragraph']) : '';
        if (($args['_fromCIF'] ?? null) === true) {
            $args['internalLinkCID'] = empty($args['internalLinkCID']) ? 0 : (int) $args['internalLinkCID'];
        } else {
            [$linkHandle, $linkValue] = $this->app->make(DestinationPicker::class)->decode('link', $this->getLinkDestinationPickers(), $errors, t('Link'), $args);
            $args['externalLink'] = $linkHandle === 'external_url' ? $security->sanitizeURL($linkValue) : '';
            $args['internalLinkCID'] = $linkHandle === 'page' ? $linkValue : 0;
        }
        if ($errors->has()) {
            throw new UserMessageException($errors->toText());
        }
        parent::save($args);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::getImportData()
     */
    protected function getImportData($blockNode, $page)
    {
        return parent::getImportData($blockNode, $page) + ['_fromCIF' => true];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\File\Tracker\FileTrackableInterface::getUsedFiles()
     */
    public function getUsedFiles()
    {
        $result = $this->app->make(RichTextExtractor::class)->extractFiles($this->paragraph);
        if ($this->fID) {
            $result[] = (int) $this->fID;
        }

        return $result;
    }

    protected function getLinkDestinationPickers(): array
    {
        return [
            'none',
            'page',
            'external_url' => ['maxlength' => 255],
        ];
    }
}
