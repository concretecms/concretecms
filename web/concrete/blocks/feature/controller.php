<?php

namespace Concrete\Block\Feature;
use Page;
use Loader;

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Block\BlockController;
use Less_Parser;
use Less_Tree_Rule;
use Core;

class Controller extends BlockController
{

    public $helpers = array('form');

    protected $btInterfaceWidth = 400;
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = true;
    protected $btCacheBlockOutputForRegisteredUsers = true;
    protected $btExportPageColumns = array('internalLinkCID');
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

    function getLinkURL()
    {
        if (!empty($this->externalLink)) {
            return $this->externalLink;
        } else {
            if (!empty($this->internalLinkCID)) {
                $linkToC = Page::getByID($this->internalLinkCID);
                return (empty($linkToC) || $linkToC->error) ? '' : Loader::helper('navigation')->getLinkToCollection(
                    $linkToC
                );
            } else {
                return '';
            }
        }
    }

    public function registerViewAssets()
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
        $this->edit();
    }

    public function view()
    {
        $this->set('linkURL', $this->getLinkURL());
    }

    protected function getIconClasses()
    {
        $iconLessFile = DIR_BASE_CORE . '/css/build/vendor/font-awesome/variables.less';
        $icons = array();

        $l = new Less_Parser();
        $parser = $l->parseFile($iconLessFile, false, true);
        $rules = $parser->rules;

        foreach ($rules as $rule) {
            if ($rule instanceof Less_Tree_Rule) {
                if (strpos($rule->name, '@fa-var') === 0) {
                    $name = str_replace('@fa-var-', '', $rule->name);
                    $icons[] = $name;
                }
            }
        }
        asort($icons);
        return $icons;
    }

    public function edit()
    {
        $this->requireAsset('css', 'font-awesome');
        $classes = $this->getIconClasses();

        // let's clean them up
        $icons = array('' => t('Choose Icon'));
        $txt = Core::make('helper/text');
        foreach ($classes as $class) {
            $icons[$class] = $txt->unhandle($class);
        }
        $this->set('icons', $icons);
    }

    public function getSearchableContent()
    {
        return $this->title . ' ' . $this->paragraph;
    }

    public function save($args)
    {
        switch (intval($args['linkType'])) {
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
        unset($args['linkType']);
        parent::save($args);
    }


}