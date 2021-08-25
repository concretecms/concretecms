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

    protected $btDefaultSet = 'basic';
    protected $btInterfaceWidth = 640;
    protected $btInterfaceHeight = 500;
    protected $btTable = 'btFeatureLink';
    protected $btCacheBlockRecord = true;
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = true;
    protected $btCacheBlockOutputForRegisteredUsers = true;
    protected $btCacheBlockOutputLifetime = 300;
    protected $btIgnorePageThemeGridFrameworkContainer = true;

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
        if (!empty($this->externalLink)) {
            $sec = $this->app->make('helper/security');
            $linkUrl = $sec->sanitizeURL($this->externalLink);
        } elseif (!empty($this->internalLinkCID)) {
            $linkToC = Page::getByID($this->internalLinkCID);
            if (is_object($linkToC) && !$linkToC->isError()) {
                $linkUrl = $linkToC->getCollectionLink();
            }
        } elseif (!empty($this->fileLinkID)) {
            $fileLinkObject = File::getByID($this->fileLinkID);
            if (is_object($fileLinkObject)) {
                $linkUrl = $fileLinkObject->getRelativePath();
            }
        }

        return $linkUrl;
    }

    public function view()
    {
        if ($this->buttonText) {
            $button = new Link($this->getLinkURL(), $this->buttonText);

            $theme = Theme::getSiteTheme();
            if ($theme && $theme->supportsFeature(Features::TYPOGRAPHY)) {
                $button->addClass('btn');
                $styleClass = '';
                if ($this->buttonStyle == 'outline') {
                    $styleClass = 'outline-';
                }
                $colorClass = 'btn-' . $styleClass . $this->buttonColor;
                $button->addClass($colorClass);
                if ($this->buttonSize) {
                    $button->addClass('btn-' . $this->buttonSize);
                }
            }
            $this->set('button', $button);
        }
    }

    public function save($args)
    {
        list($imageLinkType, $imageLinkValue) = $this->app->make(DestinationPicker::class)->decode('imageLink', $this->getImageLinkPickers(), null, null, $args);

        $args['buttonInternalLinkCID'] = $imageLinkType === 'page' ? $imageLinkValue : 0;
        $args['buttonFileLinkID'] = $imageLinkType === 'file' ? $imageLinkValue : 0;
        $args['buttonExternalLink'] = $imageLinkType === 'external_url' ? $imageLinkValue : '';

        parent::save($args);
    }

}
