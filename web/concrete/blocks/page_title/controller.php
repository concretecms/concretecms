<?php

namespace Concrete\Block\PageTitle;
use Page;
use \Concrete\Core\Block\BlockController;

defined('C5_EXECUTE') or die("Access Denied.");

class Controller extends BlockController
{

    public $helpers = array('form');

    protected $btInterfaceWidth = 400;
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = true;
    protected $btCacheBlockOutputForRegisteredUsers = false;
    protected $btInterfaceHeight = 200;
    protected $btTable = 'btPageTitle';
    protected $btWrapperClass = 'ccm-ui';


    public function getBlockTypeDescription()
    {
        return t("Displays a Page's Title");
    }

    public function getBlockTypeName()
    {
        return t("Page Title");
    }

    public function getSearchableContent()
    {
        return $this->getTitleText();
    }

    function getTitleText()
    {
        if ($this->useCustomTitle && strlen($this->titleText)) {
            $title = $this->titleText;
        } else {
            $p = Page::getCurrentPage();
            if ($p instanceof Page) {
                $title = $p->getCollectionName();
                if (!strlen($title) && $p->isMasterCollection()) {
                    $title = '[' . t('Page Title') . ']';
                }
            }
        }
        return $title;
    }


    public function view()
    {
        $this->set('title', $this->getTitleText());
    }


    public function save($data)
    {
        $data['useCustomTitle'] = ($data['useCustomTitle'] ? 1 : 0);
        parent::save($data);
    }

}

?>
