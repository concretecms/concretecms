<?php
namespace Concrete\Block\Faq;

use Concrete\Core\Block\BlockController;
use Loader;

class Controller extends BlockController
{
    protected $btTable = 'btFaq';
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
        return t("Frequently Asked Questions");
    }

    public function getSearchableContent()
    {
        return $this->content;
    }

    public function edit()
    {
        $db = Loader::db();
        $query = $db->GetAll('SELECT * from btFaqEntries WHERE bID = ? ORDER BY sortOrder', array($this->bID));
        $this->set('rows', $query);
    }

    public function view()
    {
        $db = Loader::db();
        $query = $db->GetAll('SELECT * from btFaqEntries WHERE bID = ? ORDER BY sortOrder', array($this->bID));
        $this->set('rows', $query);
    }

    public function delete()
    {
        $db = Loader::db();
        $db->execute('DELETE from btFaqEntriesWHERE bID = ?', array($this->bID));
        parent::delete();
    }

    public function save($args)
    {
        $db = Loader::db();
        $db->execute('DELETE from btFaqEntries WHERE bID = ?', array($this->bID));
        $count = count($args['sortOrder']);
        $i = 0;
        parent::save($args);
        while ($i < $count) {
            $db->execute('INSERT INTO btFaqEntries (bID, title, linkTitle, description, sortOrder) values(?,?,?,?,?)',
                array(
                    $this->bID,
                    $args['title'][$i],
                    $args['linkTitle'][$i],
                    $args['description'][$i],
                    $args['sortOrder'][$i]
                )
            );
            $i++;
        }
    }

}