<?php
namespace Concrete\Block\Feature;

use Concrete\Core\Editor\LinkAbstractor;
use Concrete\Core\Feature\Features;
use Concrete\Core\Feature\UsesFeatureInterface;
use Concrete\Core\Html\Service\FontAwesomeIcon;
use Concrete\Core\Validation\SanitizeService;
use Page;
use Concrete\Core\Block\BlockController;
use Core;
use Concrete\Core\File\File;

class Controller extends BlockController implements UsesFeatureInterface
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
    protected $btExportPageColumns = array('internalLinkCID');
    protected $btExportFileColumns = array('fID');
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

                return (empty($linkToC) || $linkToC->error) ? '' : Core::make('helper/navigation')->getLinkToCollection(
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
        switch (isset($args['linkType']) ? intval($args['linkType']) : 0) {
            case 1:
                $args['externalLink'] = '';
                break;
            case 2:
                $args['internalLinkCID'] = 0;
                break;
            default:
                $args['externalLink'] = '';
                $args['internalLinkCID'] = 0;
                break;
        }
        $args['paragraph'] = LinkAbstractor::translateTo($args['paragraph']);
        /** @var SanitizeService $security */
        $security = $this->app->make('helper/security');
        $args['icon'] = isset($args['icon']) ? $security->sanitizeString($args['icon']) : '';
        $args['title'] = $security->sanitizeString($args['title']);
        $args['titleFormat'] = $security->sanitizeString($args['titleFormat']);
        $args['internalLinkCID'] = $security->sanitizeInt($args['internalLinkCID']);
        $args['externalLink'] = $security->sanitizeURL($args['externalLink']);
        unset($args['linkType']);

        $args = $args + [
            'fID' => 0,
        ];
        $args['fID'] = $args['fID'] != '' ? $args['fID'] : 0;
        parent::save($args);
    }

    public function getUsedFiles()
    {
        return [$this->getFileID()];
    }
    
}
