<?php

namespace Concrete\Block\Faq;

use Concrete\Core\Block\BlockController;
use Database;

class Controller extends BlockController
{
    protected $btTable = 'btFaq';
    protected $btExportTables = array('btFaq', 'btFaqEntries');
    protected $btInterfaceWidth = "600";
    protected $btWrapperClass = 'ccm-ui';
    protected $btInterfaceHeight = "465";
    protected $btCacheBlockRecord = true;
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = true;
    protected $btCacheBlockOutputForRegisteredUsers = true;

    public function getBlockTypeDescription()
    {
        return t("Frequently Asked Questions Block");
    }

    public function getBlockTypeName()
    {
        return t("FAQ");
    }

    public function getSearchableContent()
    {
        $content = '';
        $db = Database::connection();
        $v = array($this->bID);
        $q = 'select * from btFaqEntries where bID = ?';
        $r = $db->query($q, $v);
        foreach ($r as $row) {
            $content .= $row['title'] . ' ' . $row['linkTitle'] . ' ' . $row['description'];
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
        $this->add();

        $db = Database::connection();
        $query = $db->GetAll('SELECT * from btFaqEntries WHERE bID = ? ORDER BY sortOrder', array($this->bID));
        $this->set('rows', $query);
    }

    public function view()
    {
        $db = Database::connection();
        $query = $db->GetAll('SELECT * from btFaqEntries WHERE bID = ? ORDER BY sortOrder', array($this->bID));
        $this->set('rows', $query);
    }

    public function duplicate($newBID)
    {
        $db = Database::connection();
        $v = array($this->bID);
        $q = 'select * from btFaqEntries where bID = ?';
        $r = $db->query($q, $v);
        foreach ($r as $row) {
            $db->execute(
                'INSERT INTO btFaqEntries (bID, title, linkTitle, description, sortOrder) values(?,?,?,?,?)',
                array(
                    $newBID,
                    $row['title'],
                    $row['linkTitle'],
                    $row['description'],
                    $row['sortOrder'],
                )
            );
        }
    }

    public function delete()
    {
        $db = Database::connection();
        $db->execute('DELETE from btFaqEntries WHERE bID = ?', array($this->bID));
        parent::delete();
    }

    public function save($args)
    {
        $db = Database::connection();
        $db->execute('DELETE from btFaqEntries WHERE bID = ?', array($this->bID));
        $count = isset($args['sortOrder']) ? count($args['sortOrder']) : 0;
        $i = 0;
        parent::save($args);
        while ($i < $count) {
            $db->execute(
                'INSERT INTO btFaqEntries (bID, title, linkTitle, description, sortOrder) values(?,?,?,?,?)',
                array(
                    $this->bID,
                    $args['title'][$i],
                    $args['linkTitle'][$i],
                    $args['description'][$i],
                    $args['sortOrder'][$i],
                )
            );
            ++$i;
        }
    }
}
