<?php
namespace Concrete\Block\Faq;

use Concrete\Core\Block\BlockController;

class Controller extends BlockController
{
    protected $btInterfaceWidth = 600;
    protected $btInterfaceHeight = 465;
    protected $btTable = 'btFaq';
    protected $btExportTables = ['btFaq', 'btFaqEntries'];
    protected $btWrapperClass = 'ccm-ui';
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = true;
    protected $btCacheBlockOutputForRegisteredUsers = true;

    public function getBlockTypeName()
    {
        return t('FAQ');
    }

    public function getBlockTypeDescription()
    {
        return t('Frequently Asked Questions Block');
    }

    public function getSearchableContent()
    {
        $content = '';
        $db = $this->app->make('database')->connection();
        $v = [$this->bID];
        $q = 'SELECT * FROM btFaqEntries WHERE bID = ?';
        $r = $db->query($q, $v);
        foreach ($r as $row) {
            $content .= $row['title'] . ' ' . $row['linkTitle'] . ' ' . $row['description'];
        }

        return $content;
    }

    public function edit()
    {
        $db = $this->app->make('database')->connection();
        $query = $db->GetAll('SELECT * FROM btFaqEntries WHERE bID = ? ORDER BY sortOrder', [$this->bID]);
        $this->set('rows', $query);
    }

    public function view()
    {
        $db = $this->app->make('database')->connection();
        $query = $db->GetAll('SELECT * FROM btFaqEntries WHERE bID = ? ORDER BY sortOrder', [$this->bID]);
        $this->set('rows', $query);
    }

    public function duplicate($newBID)
    {
        $db = $this->app->make('database')->connection();
        $v = [$this->bID];
        $q = 'SELECT * FROM btFaqEntries WHERE bID = ?';
        $r = $db->query($q, $v);
        foreach ($r as $row) {
            $db->execute(
                'INSERT INTO btFaqEntries (bID, title, linkTitle, description, sortOrder) VALUES(?,?,?,?,?)',
                [
                    $newBID,
                    $row['title'],
                    $row['linkTitle'],
                    $row['description'],
                    $row['sortOrder'],
                ]
            );
        }
    }

    public function delete()
    {
        $db = $this->app->make('database')->connection();
        $db->execute('DELETE FROM btFaqEntries WHERE bID = ?', [$this->bID]);
        parent::delete();
    }

    public function save($args)
    {
        $db = $this->app->make('database')->connection();
        $db->execute('DELETE FROM btFaqEntries WHERE bID = ?', [$this->bID]);
        $count = isset($args['sortOrder']) ? count($args['sortOrder']) : 0;

        $i = 0;
        parent::save($args);
        while ($i < $count) {
            $db->execute(
                'INSERT INTO btFaqEntries (bID, title, linkTitle, description, sortOrder) VALUES(?,?,?,?,?)',
                [
                    $this->bID,
                    $args['title'][$i],
                    $args['linkTitle'][$i],
                    $args['description'][$i],
                    $args['sortOrder'][$i],
                ]
            );
            ++$i;
        }
    }
}
