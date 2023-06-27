<?php

namespace Concrete\Block\FeatureLink;

use Concrete\Core\Application\Service\FileManager;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Feature\Features;
use Concrete\Core\Feature\UsesFeatureInterface;
use Concrete\Core\File\File;
use Concrete\Core\Form\Service\DestinationPicker\DestinationPicker;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Permission\Checker;
use HtmlObject\Link;
use Concrete\Core\Html\Service\FontAwesomeIcon;


defined('C5_EXECUTE') or die('Access Denied.');

class Controller extends BlockController implements UsesFeatureInterface
{
    /**
     * @var string|null
     */
    public $title;

    /**
     * @var string|null
     */
    public $body;

    /**
     * @var string|null
     */
    public $buttonText;

    /**
     * @var string|null
     */
    public $buttonExternalLink;

    /**
     * @var int|string|null
     */
    public $buttonInternalLinkCID;

    /**
     * @var int|string|null
     */
    public $buttonFileLinkID;

    /**
     * @var string|null
     */
    public $buttonColor;

    /**
     * @var string|null
     */
    public $buttonStyle;

    /**
     * @var string|null
     */
    public $buttonSize;

    /**
     * @var string|null
     */
    public $titleFormat;

    /**
     * @var string|null
     */
    protected $icon;

    /**
     * @var int|string|null
     */
    public $fID;

    public $helpers = ['form'];

    public $buttonIcon;

    protected $btDefaultSet = 'basic';
    protected $btInterfaceWidth = 640;
    protected $btInterfaceHeight = 500;
    protected $btTable = 'btFeatureLink';
    protected $btCacheBlockRecord = true;
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = true;
    protected $btExportFileColumns = array('fID');
    protected $btExportPageColumns = ['imageLink_page'];
    protected $btCacheBlockOutputForRegisteredUsers = true;
    protected $btCacheBlockOutputLifetime = 300;

    /**
     * {@inheritdoc}
     */
    public function getBlockTypeName()
    {
        return t('Feature Link');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockTypeDescription()
    {
        return t('Add a title, body and a button/link to a page. Useful for calling out important links.');
    }

    /**
     * @return string[]
     */
    protected function getImageLinkPickers()
    {
        return [
            'none',
            'page',
            'file',
            'external_url' => ['maxlength' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredFeatures(): array
    {
        return [
            Features::BASICS,
        ];
    }

    public function add()
    {
        $this->set('titleFormat', 'h2');
        $this->edit();
        $this->set('bf', null);
    }

    public function edit()
    {
        $theme = Theme::getSiteTheme();
        $this->set('editor', $this->app->make('editor'));
        $this->set('destinationPicker', $this->app->make(DestinationPicker::class));
        $this->set('imageLinkPickers', $this->getImageLinkPickers());
        $this->set('themeColorCollection', $theme->getColorCollection());
        if ($this->buttonInternalLinkCID) {
            $this->set('imageLinkHandle', 'page');
            $this->set('imageLinkValue', $this->buttonInternalLinkCID);
        } elseif ($this->buttonFileLinkID) {
            $this->set('imageLinkHandle', 'file');
            $this->set('imageLinkValue', $this->buttonFileLinkID);
        } elseif ((string) $this->buttonExternalLink !== '') {
            $this->set('imageLinkHandle', 'external_url');
            $this->set('imageLinkValue', $this->buttonExternalLink);
        } else {
            $this->set('imageLinkHandle', 'none');
            $this->set('imageLinkValue', null);
        }
         // Image file object
         $bf = null;
         if ($this->getFileID() > 0) {
             $bf = $this->getFileObject();
         }
         $this->set('bf', $bf);
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

    /**
     * @TODO - move all this logic into the DestinationPicker somehow. Make the destination picker save
     * its object into some kind of special destination object. Refactor destinationpicker into
     * vue component.
     *
     * @return string
     */
    public function getLinkURL()
    {
        $linkUrl = '';
        if (!empty($this->buttonExternalLink)) {
            $sec = $this->app->make('helper/security');
            $linkUrl = $sec->sanitizeURL($this->buttonExternalLink);
        } elseif (!empty($this->buttonInternalLinkCID)) {
            $linkToC = Page::getByID($this->buttonInternalLinkCID);
            if (is_object($linkToC) && !$linkToC->isError()) {
                $linkUrl = $linkToC->getCollectionLink();
            }
        } elseif (!empty($this->buttonFileLinkID)) {
            $fileLinkObject = File::getByID($this->buttonFileLinkID);
            if (is_object($fileLinkObject)) {
                $linkUrl = $fileLinkObject->getRelativePath();
            }
        }

        return $linkUrl;
    }

    public function view()
    {
      if ($this->buttonText || $this->getLinkURL()) {

        $button = new Link($this->getLinkURL(), $this->buttonText);
        $this->set('button', $button);

        $theme = Theme::getSiteTheme();
        if ($theme && $theme->supportsFeature(Features::TYPOGRAPHY)) {
          $this->set('theme', $theme);
        }

        $this->set('button', $button);
        $this->set('linkURL', $this->getLinkURL());
        $this->set('buttonIcon', $this->icon);
        $this->set('iconTag', FontAwesomeIcon::getFromClassNames(h($this->icon)));
      }
      // Check for a valid File in the view
      $f = $this->getFileObject();
      $this->set('f', $f);
    }

    public function save($args)
    {
        list($imageLinkType, $imageLinkValue) = $this->app->make(DestinationPicker::class)->decode('imageLink', $this->getImageLinkPickers(), null, null, $args);

        $args['buttonInternalLinkCID'] = $imageLinkType === 'page' ? $imageLinkValue : 0;
        $args['buttonFileLinkID'] = $imageLinkType === 'file' ? $imageLinkValue : 0;
        $args['buttonExternalLink'] = $imageLinkType === 'external_url' ? $imageLinkValue : '';
        $security = $this->app->make('helper/security');
        $args['icon'] = $security->sanitizeString($args['icon'] ?? '');
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
