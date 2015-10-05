<?php

namespace Concrete\Block\ImageSlider;

use Concrete\Core\Block\BlockController;
use Database;
use Page;

class Controller extends BlockController
{
    protected $btTable = 'btImageSlider';
    protected $btExportTables = array('btImageSlider', 'btImageSliderEntries');
    protected $btInterfaceWidth = "600";
    protected $btWrapperClass = 'ccm-ui';
    protected $btInterfaceHeight = "465";
    protected $btCacheBlockRecord = true;
    protected $btExportFileColumns = array('fID');
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = true;
    protected $btCacheBlockOutputForRegisteredUsers = false;
    protected $btIgnorePageThemeGridFrameworkContainer = true;

    public function getBlockTypeDescription()
    {
        return t("Display your images and captions in an attractive slideshow format.");
    }

    public function getBlockTypeName()
    {
        return t("Image Slider");
    }

    public function getSearchableContent()
    {
        $content = '';
        $db = Database::get();
        $v = array($this->bID);
        $q = 'select * from btImageSliderEntries where bID = ?';
        $r = $db->query($q, $v);
        foreach ($r as $row) {
            $content .= $row['title'].' ';
            $content .= $row['description'].' ';
        }

        return $content;
    }

    public function add()
    {
        $this->requireAsset('core/file-manager');
        $this->requireAsset('core/sitemap');
        $this->requireAsset('redactor');
    }

    public function edit()
    {
        $this->requireAsset('core/file-manager');
        $this->requireAsset('core/sitemap');
        $this->requireAsset('redactor');
        $db = Database::get();
        $query = $db->GetAll('SELECT * from btImageSliderEntries WHERE bID = ? ORDER BY sortOrder', array($this->bID));
        $this->set('rows', $query);
    }

    public function composer()
    {
        $this->edit();
    }

    public function registerViewAssets($outputContent = '')
    {
        $this->requireAsset('javascript', 'jquery');

        $al = \Concrete\Core\Asset\AssetList::getInstance();
        $al->register('javascript', 'responsiveslides', 'blocks/image_slider/responsiveslides.js');
        $this->requireAsset('javascript', 'responsiveslides');
    }

    public function getEntries()
    {
        $db = Database::get();
        $r = $db->GetAll('SELECT * from btImageSliderEntries WHERE bID = ? ORDER BY sortOrder', array($this->bID));
        // in view mode, linkURL takes us to where we need to go whether it's on our site or elsewhere
        $rows = array();
        foreach ($r as $q) {
            if (!$q['linkURL'] && $q['internalLinkCID']) {
                $c = Page::getByID($q['internalLinkCID'], 'ACTIVE');
                $q['linkURL'] = $c->getCollectionLink();
                $q['linkPage'] = $c;
            }
            $rows[] = $q;
        }

        return $rows;
    }

    public function view()
    {
        $this->set('rows', $this->getEntries());
    }

    public function duplicate($newBID)
    {
        parent::duplicate($newBID);
        $db = Database::get();
        $v = array($this->bID);
        $q = 'select * from btImageSliderEntries where bID = ?';
        $r = $db->query($q, $v);
        while ($row = $r->FetchRow()) {
            $db->execute('INSERT INTO btImageSliderEntries (bID, fID, linkURL, title, description, sortOrder) values(?,?,?,?,?,?)',
                array(
                    $newBID,
                    $row['fID'],
                    $row['linkURL'],
                    $row['title'],
                    $row['description'],
                    $row['sortOrder'],
                )
            );
        }
    }

    public function delete()
    {
        $db = Database::get();
        $db->delete('btImageSliderEntries', array('bID' => $this->bID));
        parent::delete();
    }

    public function save($args)
    {
        $db = Database::get();
        $db->execute('DELETE from btImageSliderEntries WHERE bID = ?', array($this->bID));
        parent::save($args);
        if (isset($args['sortOrder'])) {
            $count = count($args['sortOrder']);
            $i = 0;

            while ($i < $count) {
                $linkURL = $args['linkURL'][$i];
                $internalLinkCID = $args['internalLinkCID'][$i];
                switch (intval($args['linkType'][$i])) {
                    case 1:
                        $linkURL = '';
                        break;
                    case 2:
                        $internalLinkCID = 0;
                        break;
                    default:
                        $linkURL = '';
                        $internalLinkCID = 0;
                        break;
                }

                $db->execute('INSERT INTO btImageSliderEntries (bID, fID, title, description, sortOrder, linkURL, internalLinkCID) values(?, ?, ?, ?,?,?,?)',
                    array(
                        $this->bID,
                        intval($args['fID'][$i]),
                        $args['title'][$i],
                        $args['description'][$i],
                        $args['sortOrder'][$i],
                        $linkURL,
                        $internalLinkCID,
                    )
                );
                ++$i;
            }
        }
    }
}
