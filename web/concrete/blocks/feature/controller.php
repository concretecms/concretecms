<?php

namespace Concrete\Block\Feature;
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
    protected $btInterfaceHeight = 360;
    protected $btTable = 'btFeature';

    public function getBlockTypeDescription()
    {
        return t("Displays an icon, a title, and a short paragraph description.");
    }

    public function getBlockTypeName()
    {
        return t("Feature");
    }

    public function on_start()
    {
        $this->requireAsset('css', 'font-awesome');
    }

    public function add()
    {
        $this->edit();
    }

    protected function getIconClasses()
    {
        $iconLessFile = DIR_BASE_CORE . '/css/build/vendor/font-awesome/variables.less';
        $icons = array();

        $l = new Less_Parser();
        $parser = $l->parseFile($iconLessFile, false, true);
        $rules = $parser->rules;

        foreach($rules as $rule) {
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
        $classes = $this->getIconClasses();

        // let's clean them up
        $icons = array('' => t('Choose Icon'));
        $txt = Core::make('helper/text');
        foreach($classes as $class) {
            $icons[$class] = $txt->unhandle($class);
        }
        $this->set('icons', $icons);
    }

    public function getSearchableContent()
    {
        return $this->description;
    }


}