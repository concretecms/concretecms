<?php

namespace Concrete\Block\Feature;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Editor\LinkAbstractor;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Feature\Features;
use Concrete\Core\Feature\UsesFeatureInterface;
use Concrete\Core\Html\Service\FontAwesomeIcon;
use Concrete\Core\Page\Page;
use Concrete\Core\Validation\SanitizeService;

class Controller extends BlockController implements UsesFeatureInterface
{
    /**
     * @var string[]
     */
    public $helpers = ['form', 'form/page_selector'];

    /**
     * @var int
     */
    protected $btInterfaceWidth = 400;

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
     * @var string[]
     */
    protected $btExportPageColumns = ['internalLinkCID'];

    /**
     * @var int
     */
    protected $btInterfaceHeight = 520;

    /**
     * @var string
     */
    protected $btTable = 'btFeature';

    /**
     * @var string|null
     */
    protected $icon;

    /**
     * @var string|null
     */
    protected $title;

    /**
     * @var string|null
     */
    protected $paragraph;

    /**
     * @return string
     */
    public function getBlockTypeDescription()
    {
        return t('Displays an icon, a title, and a short paragraph description.');
    }

    /**
     * @return string
     */
    public function getBlockTypeName()
    {
        return t('Feature');
    }

    /**
     * @return string[]
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
     * @return string
     */
    public function getLinkURL()
    {
        if (!empty($this->externalLink)) {
            return $this->externalLink;
        }
            if (!empty($this->internalLinkCID)) {
                /** @var Page|null $linkToC */
                $linkToC = Page::getByID($this->internalLinkCID);

                return ($linkToC === null || $linkToC->isError()) ? '' : $this->app->make('helper/navigation')->getLinkToCollection(
                    $linkToC
                );
            }

                return '';
    }

    /**
     * @return string
     */
    public function getParagraph()
    {
        return LinkAbstractor::translateFrom($this->paragraph);
    }

    /**
     * @return string
     */
    public function getParagraphEditMode()
    {
        return LinkAbstractor::translateFromEditMode($this->paragraph);
    }

    /**
     * @param string $outputContent
     *
     * @return void
     */
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

    /**
     * @return void
     */
    public function add()
    {
        $this->set('titleFormat', 'h4');
        $this->edit();
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return void
     */
    public function view()
    {
        $this->set('iconTag', FontAwesomeIcon::getFromClassNames(h($this->icon)));
        $this->set('paragraph', LinkAbstractor::translateFrom($this->paragraph));
        $this->set('linkURL', $this->getLinkURL());
    }

    /**
     * @return void
     */
    public function edit()
    {
        $this->requireAsset('css', 'font-awesome');
    }

    /**
     * @return string
     */
    public function getSearchableContent()
    {
        return $this->title . ' ' . $this->paragraph;
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
        switch (isset($args['linkType']) ? (int) ($args['linkType']) : 0) {
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
        $args['icon'] = $security->sanitizeString($args['icon']);
        $args['title'] = $security->sanitizeString($args['title']);
        $args['titleFormat'] = $security->sanitizeString($args['titleFormat']);
        $args['internalLinkCID'] = $security->sanitizeInt($args['internalLinkCID']);
        $args['externalLink'] = $security->sanitizeURL($args['externalLink']);
        unset($args['linkType']);
        parent::save($args);
    }

    /**
     * Used to validate a block's data before saving to the database
     * Generally should return an empty ErrorList if valid
     * Custom Packages may return a boolean value.
     *
     * @param array<string>|string|null $args
     *
     * @version 9.0.3a1 Method added to feature block
     *
     * @return ErrorList
     */
    public function validate($args)
    {
        /** @var ErrorList $e */
        $e = parent::validate($args);

        if (empty($args['paragraph']) && empty($args['title'])) {
            $e->add(t('You must enter a description or title.'));
        }

        return $e;
    }
}
