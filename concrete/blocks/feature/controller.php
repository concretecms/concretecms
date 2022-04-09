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

class Controller extends BlockController implements UsesFeatureInterface
{
    public $helpers = array('form');

    protected $btInterfaceWidth = 400;
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = true;
    protected $btCacheBlockOutputForRegisteredUsers = true;
    protected $btExportPageColumns = array('internalLinkCID');
    protected $btInterfaceHeight = 520;
    protected $btTable = 'btFeature';

    protected $icon;

    public $paragraph;

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
    }

    public function view()
    {
        $this->set('iconTag', FontAwesomeIcon::getFromClassNames(h($this->icon)));
        $this->set('paragraph', LinkAbstractor::translateFrom($this->paragraph));
        $this->set('linkURL', $this->getLinkURL());
    }

    public function edit()
    {
        $this->requireAsset('css', 'font-awesome');
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
        $args['icon'] = $security->sanitizeString($args['icon']);
        $args['title'] = $security->sanitizeString($args['title']);
        $args['titleFormat'] = $security->sanitizeString($args['titleFormat']);
        $args['internalLinkCID'] = $security->sanitizeInt($args['internalLinkCID']);
        $args['externalLink'] = $security->sanitizeURL($args['externalLink']);
        unset($args['linkType']);
        parent::save($args);
    }
}
