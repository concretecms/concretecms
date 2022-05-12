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
    public $helpers = ['form'];

    public $buttonInternalLinkCID;
    public $buttonExternalLink;
    public $buttonFileLinkID;
    public $buttonText;
    public $buttonSize;
    public $buttonStyle;
    public $buttonColor;
    public $buttonIcon;

    protected $btDefaultSet = 'basic';
    protected $btInterfaceWidth = 640;
    protected $btInterfaceHeight = 500;
    protected $btTable = 'btFeatureLink';
    protected $btCacheBlockRecord = true;
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = true;
    protected $btCacheBlockOutputForRegisteredUsers = true;
    protected $btCacheBlockOutputLifetime = 300;

    protected $icon;

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
    }




    public function save($args)
    {
        list($imageLinkType, $imageLinkValue) = $this->app->make(DestinationPicker::class)->decode('imageLink', $this->getImageLinkPickers(), null, null, $args);

        $args['buttonInternalLinkCID'] = $imageLinkType === 'page' ? $imageLinkValue : 0;
        $args['buttonFileLinkID'] = $imageLinkType === 'file' ? $imageLinkValue : 0;
        $args['buttonExternalLink'] = $imageLinkType === 'external_url' ? $imageLinkValue : '';
        /** @var SanitizeService $security */
        $security = $this->app->make('helper/security');
        $args['icon'] = $security->sanitizeString($args['icon'] ?? '');

        parent::save($args);
    }
    

}
