<?php

namespace Concrete\Block\FeatureLink;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Feature\Features;
use Concrete\Core\Feature\UsesFeatureInterface;
use Concrete\Core\File\File;
use Concrete\Core\Form\Service\DestinationPicker\DestinationPicker;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Theme\Theme;
use HtmlObject\Link;
use Concrete\Core\Html\Service\FontAwesomeIcon;


defined('C5_EXECUTE') or die('Access Denied.');

class Controller extends BlockController implements UsesFeatureInterface
{
    /**
     * @var int|null
     */
    public $buttonInternalLinkCID;

    /**
     * @var string|null
     */
    public $buttonExternalLink;

    /**
     * @var int|null
     */
    public $buttonFileLinkID;

    /**
     * @var string|null
     */
    public $buttonText;

    /**
     * @var string|null
     */
    public $buttonSize;

    /**
     * @var string|null
     */
    public $buttonStyle;

    /**
     * @var string|null
     */
    public $buttonColor;
    /**
     * @var string|null
     */
    public $buttonIcon;

    /**
     * @var string
     */
    protected $btDefaultSet = 'basic';

    /**
     * @var int
     */
    protected $btInterfaceWidth = 640;

    /**
     * @var int
     */
    protected $btInterfaceHeight = 500;

    /**
     * @var string
     */
    protected $btTable = 'btFeatureLink';

    /**
     * @var bool
     */
    protected $btCacheBlockOutput = true;

    /**
     * @var bool
     */
    protected $btCacheBlockOutputOnPost = true;

    /**
     * @var bool
     */
    protected $btCacheBlockOutputForRegisteredUsers = true;

    /**
     * @var int
     */
    protected $btCacheBlockOutputLifetime = 300;
    /**
     * @var string|null
     */
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
     * {@inheritdoc}
     */
    public function getRequiredFeatures(): array
    {
        return [
            Features::BASICS,
        ];
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return void
     */
    public function add()
    {
        $this->set('titleFormat', 'h2');
        $this->edit();
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return void
     */
    public function edit()
    {
        $theme = Theme::getSiteTheme();
        $this->set('editor', $this->app->make('editor'));
        $this->set('destinationPicker', $this->app->make(DestinationPicker::class));
        $this->set('imageLinkPickers', $this->getImageLinkPickers());
        $this->set('themeColorCollection', is_object($theme) ? $theme->getColorCollection() : null);
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
            /** @var \Concrete\Core\Entity\File\Version $fileLinkObject */
            $fileLinkObject = File::getByID($this->buttonFileLinkID);
            if (is_object($fileLinkObject)) {
                $linkUrl = $fileLinkObject->getRelativePath();
            }
        }

        return $linkUrl;
    }

    /**
     * @return void
     */
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




    /**
     * @param array<string,mixed> $args
     *
     * @version 9.0.3a1 Method added to feature_link block
     *
     * @return ErrorList
     */
    public function validate($args)
    {
        /** @var ErrorList $e */
        $e = parent::validate($args);

        if (empty($args['body']) && empty($args['title']) && empty($args['buttonText'])) {
            $e->add(t('You must enter a title, body or button.'));
        }

        return $e;
    }

    /**
     * @param array<string,mixed> $args
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return void
     */
    public function save($args)
    {
        [$imageLinkType, $imageLinkValue] = $this->app->make(DestinationPicker::class)->decode(
            'imageLink',
            $this->getImageLinkPickers(),
            null,
            null,
            $args
        );

        $args['buttonInternalLinkCID'] = $imageLinkType === 'page' ? $imageLinkValue : 0;
        $args['buttonFileLinkID'] = $imageLinkType === 'file' ? $imageLinkValue : 0;
        $args['buttonExternalLink'] = $imageLinkType === 'external_url' ? $imageLinkValue : '';
        /** @var SanitizeService $security */
        $security = $this->app->make('helper/security');
        $args['icon'] = $security->sanitizeString($args['icon']);

        parent::save($args);
    }


    /**
     * @return array<int|string,string|array<string,mixed>>
     */
    protected function getImageLinkPickers(): array
    {
        return [
            'none',
            'page',
            'file',
            'external_url' => ['maxlength' => 255],
        ];
    }
}
